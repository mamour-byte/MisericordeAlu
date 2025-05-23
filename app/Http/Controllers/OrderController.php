<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Quote;
use App\Models\QuoteItem;
use Illuminate\Support\Facades\DB;
use Orchid\Support\Facades\Toast;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{

   private function generateDocumentNumber(string $type): string
    {
        $prefix = $type === 'Invoice' ? 'INV-' : 'QT-';

        if ($type === 'Invoice') {
            $lastNumber = \App\Models\Invoice::whereNotNull('no_invoice')
                ->orderByDesc('id')
                ->value('no_invoice');
        } else {
            $lastNumber = \App\Models\Quote::whereNotNull('no_quote')
                ->orderByDesc('id')
                ->value('no_quote');
        }

        if ($lastNumber) {
            // Extraire la partie numérique et incrémenter
            $number = (int) str_replace($prefix, '', $lastNumber);
            $number++;
        } else {
            $number = 1;
        }

        // Retourner le numéro formaté
        return $prefix . str_pad($number, 5, '0', STR_PAD_LEFT);
    }



    public function save(Request $request)
    {
        $data = $request->get('order');

        $productIds = $data['products'] ?? [];
        $quantities = $data['quantities'] ?? [];

        if (is_string($quantities)) {
            $quantities = explode(',', $quantities);
        }

        $documentType = $data['Docs'] ?? 'Invoice';
        $documentNumber = $this->generateDocumentNumber($documentType);

        if (count($productIds) !== count($quantities)) {
            Toast::error('Le nombre de produits ne correspond pas aux quantités.');
            return redirect()->back();
        }

        DB::beginTransaction();

        try {
            $total = 0;

            // Création de la commande (toujours)
            $order = Order::create([
                'customer_name'    => $data['customer_name'],
                'customer_email'   => $data['customer_email'],
                'customer_phone'   => $data['customer_phone'],
                'customer_address' => $data['customer_address'],
                'status'           => $documentType === 'Invoice' ? 'approved' : 'pending',
                'total_amount'     => 0,
                'user_id'          => Auth::id(),
            ]);


            $items = [];

            foreach ($productIds as $key => $productId) {
                $product = Product::findOrFail($productId);
                $quantity = (int) ($quantities[$key] ?? 0);
                $unit_price = (float) $product->price;
                $item_total = $quantity * $unit_price;

                // Création d'un OrderItem
                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                    'unit_price' => $unit_price,
                ]);

                $total += $item_total;

                // On prépare les données pour InvoiceItem ou QuoteItem
                $items[] = [
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                    'unit_price' => $unit_price,
                ];
            }

            // Mise à jour du total
            $order->update(['total_amount' => $total]);

            // Création du bon document selon le type
            if ($documentType === 'Invoice') {
                $invoice = Invoice::create([
                    'customer_name'    => $data['customer_name'],
                    'customer_email'   => $data['customer_email'],
                    'customer_phone'   => $data['customer_phone'],
                    'customer_address' => $data['customer_address'],
                    'status'           => 'approved',
                    'total_amount'     => $total,
                    'no_invoice'       => $documentType === 'Invoice' ? $documentNumber : null,
                ]);

                foreach ($items as $item) {
                    $item['invoice_id'] = $invoice->id;
                    InvoiceItem::create($item);
                }
            } else {
                $quote = Quote::create([
                    'customer_name'    => $data['customer_name'],
                    'customer_email'   => $data['customer_email'],
                    'customer_phone'   => $data['customer_phone'],
                    'customer_address' => $data['customer_address'],
                    'status'           => 'pending',
                    'total_amount'     => $total,
                    'no_quote'         => $documentType === 'Quote'   ? $documentNumber : null,
                ]);

                foreach ($items as $item) {
                    $item['quote_id'] = $quote->id;
                    QuoteItem::create($item);
                }
            }

            DB::commit();
            Toast::success("Commande et {$documentType} enregistrés avec succès.");
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            Toast::error("Une erreur est survenue : " . $e->getMessage());
        }

        return redirect()->route('platform.Commandes');
    }
}
