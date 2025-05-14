<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Orchid\Support\Facades\Alert;

class OrderController extends Controller
{
    public function save(Request $request)
    {
        $data = $request->get('order');

        // Vérification et préparation des données
        $productIds = $data['products'] ?? [];
        $quantities = $data['quantities'] ?? [];

        // Gérer le cas où les quantités sont sous forme de chaîne "1,2,3"
        if (is_string($quantities)) {
            $quantities = explode(',', $quantities);
        }

        $total = 0;

        // Création de la commande
        $order = Order::create([
            'customer_name'    => $data['customer_name'],
            'customer_email'   => $data['customer_email'],
            'customer_phone'   => $data['customer_phone'],
            'customer_address' => $data['customer_address'],
            'status'           => 'pending',
            'total_amount'     => 0, // temporaire
        ]);

        // Ajout des produits
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

            $total += $item_total;
        }

        // Mise à jour du montant total de la commande
        $order->update(['total_amount' => $total]);

        Alert::info('Commande enregistrée avec succès.');

        return redirect()->route('platform.Commandes');
    }
}
