<?php

namespace App\Orchid\Layouts\OrderTabs;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Button;
use App\Models\Order;

class OrderLayout extends Table
{
    protected $target = 'Commandes';

    protected function columns(): iterable
    {
        return [
            TD::make('customer_name', 'order'),

            TD::make('produits', 'Produits')
                ->render(function (Order $order) {
                    return $order->items->map(function ($item) {
                        $product = $item->product;
                        if (!$product) {
                            return 'Produit supprimé (x' . $item->quantity . ')';
                        }
                        return $product->name
                            . ' (x' . $item->quantity . ') - '
                            . number_format($product->price) . ' F CFA';
                    })->implode('<br>');
                }),

            TD::make('total_amount', 'Montant total')
                ->render(function (Order $order) {
                    return number_format($order->total_amount, 2) . ' F CFA';
                }),

            TD::make('created_at', 'Date de création')
                ->render(function (Order $order) {
                    return $order->created_at->format('d/m/Y H:i');
                }),

            TD::make('status', 'Statut')
                ->render(function (Order $order) {
                    return $order->status == 'pending' ? 'En attente' : 'Terminé';
                }),

            TD::make('action', 'Action')
                ->render(function (Order $order) {
                    return Button::make('Voir')
                        ->icon('eye')
                        ->method('view', [
                            'id' => $order->id,
                        ]);
                }),
        ];
    }
}
