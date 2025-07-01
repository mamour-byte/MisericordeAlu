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
use App\Services\NumberGenerator ;

class OrderController extends Controller
{
    

    public function save(Request $request)
    {
        $data = $request->get('order');
        $productIds = $data['products'] ?? [];
        $quantities = $data['quantities'] ?? [];

        $user = Auth::user();
        $shop = $user->shop;

        if (!$shop) {
            Toast::error("Aucune boutique n'est assignée à ce gérant.");
            return redirect()->back();
        }

        if (is_string($quantities)) {
            $quantities = explode(',', $quantities);
        }

        $documentType = $data['Docs'] ?? 'Invoice';
        $documentNumber = NumberGenerator::generateDocumentNumber($documentType);
        $orderNumber = NumberGenerator::generateOrderNumber();

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
                'shop_id'          => $shop->id,
            ]);

            $items = [];

            foreach ($productIds as $key => $productId) {
                $product = Product::findOrFail($productId);
                $quantity = (int) ($quantities[$key] ?? 0);
                $unit_price = (float) $product->price;
                $item_total = $quantity * $unit_price;

                // Ajout des items à la commande avec no_order
                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                    'unit_price' => $unit_price,
                    'no_order'   => $orderNumber,
                ]);

                $total += $item_total;

                $items[] = [
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                    'unit_price' => $unit_price,
                    'no_order'   => $orderNumber,
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
                    'user_id'          => Auth::id(),
                    'shop_id'          => $shop->id,
                ]);

                // Lier l'order à la facture
                $order->invoice_id = $invoice->id;
                $order->save();

                foreach ($items as $item) {
                    $item['invoice_id'] = $invoice->id;
                    $item['no_invoice'] = $documentNumber;
                    InvoiceItem::create($item);

                    // Mouvement de stock
                    StockMovement::create([
                        'product_id' => $item['product_id'],
                        'order_id'   => $order->id,
                        'shop_id'    => $shop->id,
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
                    'user_id'          => Auth::id(),
                    'shop_id'          => $shop->id,
                ]);

                // Lier l'order au devis
                $order->quote_id = $quote->id;
                $order->save();

                foreach ($items as $item) {
                    $item['quote_id'] = $quote->id;
                    $item['no_quote'] = $documentNumber;
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
