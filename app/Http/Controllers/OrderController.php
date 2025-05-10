<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use Orchid\Support\Facades\Alert;

class OrderController extends Controller
{
    public function save(Request $request)
        {
            $data = $request->get('order');

            $product = Product::findOrFail($data['product_id']);
            $unit_price = $product->price;

            $total = $data['quantity'] * $unit_price;

            $order = Order::create([
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'],
                'customer_address' => $data['customer_address'],
                'status' => 'pending',
                'total_amount' => $total,
            ]);

            $order->items()->create([
                'product_id' => $product->id,
                'quantity' => $data['quantity'],
                'unit_price' => $unit_price,
            ]);

            Alert::info('Commande enregistrée avec succès.');

            return redirect()->route('platform.Commandes');
        }

}
