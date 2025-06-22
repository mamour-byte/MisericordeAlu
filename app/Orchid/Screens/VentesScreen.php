<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use App\Models\Order;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Toast;
use App\Models\Avoir;

class VentesScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
            return [
                'Commandes' => Order::with(['items.product'])
                    ->where('archived', 'non')
                    ->latest()
                    ->paginate(10),

                'Archived' => Order::with(['items.product'])
                    ->where('archived', 'oui')
                    ->latest()
                    ->paginate(10),
            ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Liste des ventes';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }



    private function orderTableColumns()
    {
        return [
            TD::make('name', 'Boutique')
                ->render(fn(Order $order) => $order->user->shop->name ?? 'Aucune'),

            TD::make('user.name','Vendeur')
                ->sort()
                ->render(fn(Order $order) => $order->user->name ?? 'Inconnu'),

            TD::make('customer_name')
                ->sort()
                ->render(fn(Order $order) => $order->customer_name ?? 'Inconnu'),

            TD::make('total_amount')
                ->sort()
                ->render(fn(Order $order) => $order->total_amount),


            TD::make('status')
                ->sort()
                ->render(function(Order $order) {
                    $color = match($order->status) {
                        'pending' => 'text-warning',
                        'approved' => 'text-success',
                        'cancelled' => 'text-danger',
                        default => 'text-muted'
                    };
                    return "<span class='{$color}'>{$order->status}</span>";
                }),

            TD::make('created_at', 'Date')
                ->sort()
                ->render(fn(Order $order) => $order->created_at->format('d/m/Y H:i')),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::tabs([
                'Commandes' => Layout::table('Commandes', $this->orderTableColumns()),
                'Archives'  => Layout::table('Archived', $this->orderTableColumns()),
            ]),
        ];
    }

}
