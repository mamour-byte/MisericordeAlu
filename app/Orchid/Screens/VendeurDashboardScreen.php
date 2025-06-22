<?php

namespace App\Orchid\Screens;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use App\Http\Controllers\ChartController;
use App\Orchid\Layouts\Charts\OrderChart;
use App\Orchid\Layouts\Charts\VendeursOrderChart;

class VendeurDashboardScreen extends Screen
{
    public function permission(): ?iterable
    {
        return ['vendeur.dashboard'];
    }

    public function query(): iterable
    {
        $vendeurId = Auth::id();

        // Si chaque vendeur a un shop, récupère-le ainsi :
        $shop = Auth::user()->shop ?? null;

        // Vérifie que le shop existe
        $commandes = collect();
        if ($shop) {
            $commandes = Order::with(['items.product'])
                ->where('shop_id', $shop->id)
                ->where('archived', '!=', 'oui')
                ->latest()
                ->paginate(8);
        }

        // Calcul des métriques de base
        $ventesJour = Order::where('user_id', $vendeurId)
            ->whereDate('created_at', now()->toDateString())
            ->where('status', 'approved')
            ->where('archived', '!=', 'oui')
            ->count();

        $totalJour = Order::where('user_id', $vendeurId)
            ->whereDate('created_at', now()->toDateString())
            ->where('status', 'approved')
            ->where('archived', '!=', 'oui')
            ->sum('total_amount');

        $chart = app(ChartController::class);
        $ventesParProduit = $chart-> VentesParVendeur($vendeurId);

        return [
            'Commandes' => $commandes,
            'metrics' => [
                'Meilleure Vente'  => 'Produit populaire',
                'Meilleur Client' => 'Client fidèle',
                'Ventes du Jour'  => $ventesJour,
                'Total Jour'      => number_format($totalJour, 2, '.', ' ') . ' F cfa',
            ],
            'VendeurOrderData' => $ventesParProduit,
        ];
    }
    

    public function name(): ?string
    {
        return 'Espace Vendeur';
    }

    public function description(): ?string
    {
        return 'Interface simplifiée pour les vendeurs';
    }

    public function commandBar(): iterable
    {
        return [];
    }

    public function layout(): iterable
    {
        return [
            Layout::metrics([
                'Meilleure Vente'  => 'metrics.Meilleure Vente',
                'Meilleur Client'  => 'metrics.Meilleur Client',
                'Ventes du Jour'   => 'metrics.Ventes du Jour',
                'Total Jour'       => 'metrics.Total Jour',
            ]),
Layout::columns([
            Layout::table('Commandes', [
                TD::make('customer_name')
                            ->sort()
                            ->render(fn(Order $order) => $order->customer_name ?? 'Inconnu'),
                        TD::make('total_amount')
                            ->sort()
                            ->render(fn(Order $order) => $order->total_amount),
                        TD::make('status', 'Statut')
                            ->sort()
                            ->render(function (Order $order) {
                                if ($order->status === 'pending') {
                                    return '<span style="color: red; font-weight: bold;">En attente</span>';
                                } elseif ($order->status === 'approved') {
                                    return '<span style="color: green; font-weight: bold;">Validé</span>';
                                }
                                return $order->status;
                            }),
                        TD::make('Date de création')
                            ->sort()
                            ->align(TD::ALIGN_CENTER)
                            ->render(fn(Order $order) => $order->created_at),
                        ]),

            
                VendeursOrderChart::class,
            ]),
        ];
    }
}
