<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Orchid\Support\Facades\Alert;
use App\Models\StockMovement;

class OrderController extends Controller
{
    

    public function save(Request $request)
    {
        $data = $request->get('order');

        $productIds = $data['products'] ?? [];
        $quantities = $data['quantities'] ?? [];

        if (is_string($quantities)) {
            $quantities = explode(',', $quantities);
        }

        $total = 0;

        $order = Order::create([
            'customer_name'    => $data['customer_name'],
            'customer_email'   => $data['customer_email'],
            'customer_phone'   => $data['customer_phone'],
            'customer_address' => $data['customer_address'],
            'status'           => 'pending',
            'total_amount'     => 0,
        ]);

        foreach ($productIds as $key => $productId) {
            $product = Product::findOrFail($productId);
            $quantity = isset($quantities[$key]) ? (int) $quantities[$key] : 0;
            $unit_price = (float) $product->price;
            $item_total = $quantity * $unit_price;

            // Création de l’élément de commande
            $order->items()->create([
                'product_id' => $product->id,
                'quantity'   => $quantity,
                'unit_price' => $unit_price,
            ]);

            // Déduction du stock
            $product->decrement('stock_quantity', $quantity);

            // Enregistrement du mouvement de stock
            StockMovement::create([
                'product_id' => $product->id,
                'orders_id'  => $order->id,
                'type'       => StockMovement::TYPE_EXIT,
                'quantity'   => $quantity,
                'notes'      => 'Sortie stock liée à la commande #' . $order->id,
            ]);

            $total += $item_total;
        }

        $order->update(['total_amount' => $total]);

        Alert::info('Commande enregistrée avec succès.');

        return redirect()->route('platform.Commandes');
    }

}
