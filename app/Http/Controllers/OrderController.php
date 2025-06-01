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
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Orchid\Support\Facades\Toast;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    private function generateDocumentNumber(string $type): string
    {
        $prefix = $type === 'Invoice' ? 'INV-' : 'QT-';

        $lastNumber = $type === 'Invoice'
            ? Invoice::whereNotNull('no_invoice')->orderByDesc('id')->value('no_invoice')
            : Quote::whereNotNull('no_quote')->orderByDesc('id')->value('no_quote');

        if ($lastNumber) {
            $number = (int) str_replace($prefix, '', $lastNumber);
            $number++;
        } else {
            $number = 1;
        }

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

            // Création de la commande
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

                // Ajout des items à la commande
                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                    'unit_price' => $unit_price,
                ]);

                $total += $item_total;

                $items[] = [
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                    'unit_price' => $unit_price,
                ];
            }

            // Mise à jour du montant total
            $order->update(['total_amount' => $total]);

            // Création du document et liaison avec la commande
            if ($documentType === 'Invoice') {
                $invoice = Invoice::create([
                    'customer_name'    => $data['customer_name'],
                    'customer_email'   => $data['customer_email'],
                    'customer_phone'   => $data['customer_phone'],
                    'customer_address' => $data['customer_address'],
                    'status'           => 'approved',
                    'total_amount'     => $total,
                    'no_invoice'       => $documentNumber,
                ]);

                // Lier l'order à la facture
                $order->invoice_id = $invoice->id;
                $order->save();

                foreach ($items as $item) {
                    $item['invoice_id'] = $invoice->id;
                    InvoiceItem::create($item);

                    // Mouvement de stock
                    StockMovement::create([
                        'product_id' => $item['product_id'],
                        'orders_id'  => $order->id,
                        'type'       => StockMovement::TYPE_EXIT,
                        'quantity'   => $item['quantity'],
                        'notes'      => 'Vente générée par commande #' . $order->id,
                    ]);
                }
            } else {
                $quote = Quote::create([
                    'customer_name'    => $data['customer_name'],
                    'customer_email'   => $data['customer_email'],
                    'customer_phone'   => $data['customer_phone'],
                    'customer_address' => $data['customer_address'],
                    'status'           => 'pending',
                    'total_amount'     => $total,
                    'no_quote'         => $documentNumber,
                ]);

                // Lier l'order au devis
                $order->quote_id = $quote->id;
                $order->save();

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
