<?php

declare(strict_types=1);

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use App\Http\Controllers\ChartController;
use App\Orchid\Layouts\Charts\ProductChart;
use App\Orchid\Layouts\Charts\OrderChart;
use App\Orchid\Layouts\Charts\UserSellingChart;
use App\Orchid\Layouts\Charts\DocsChart;
use App\Orchid\Layouts\Charts\MeilleursVendeursLayout;
use App\Orchid\Layouts\OrderTabs\OrderLayout;
use App\Models\Order;
use Orchid\Screen\TD;
use Carbon\Carbon;

class PlatformScreen extends Screen
{
    /**
     * Récupère les données à afficher.
     *
     * @return array
     */
    public function query(): iterable
        {
            $user = auth()->user();

            if (!$user->shop) {
                Toast::error("Aucun magasin ne vous a été attribué. Veuillez contacter l'administrateur.");
                return [
                    'Commandes' => collect(), 
                ];
            }


            $chart = app(ChartController::class);
            $ventesParProduit = $chart->VentesParProduit();
            $ventesSemaine = $chart->VentesSemaine();
            $meilleursVendeurs = $chart->MeilleursVendeurs();
            $venteParUser= $chart->VentesParUser();


            $jours = $chart->TotalJour();
            $mois = $chart->TotalMois();
            return [
                'metrics' => [
                    'Meilleur Vente'  => $chart->MeilleurVenteMois(),
                    'Meilleur Client' => $chart->MeilleurClientMois(),
                    'Ventes du Jour'  => $chart->VentesJour(),
                    'Total du jour'  => [
                        'value' => number_format($jours['value'],0, '.', ' ') . ' F',
                        'diff'  => $jours['diff'],
                    ],
                    'Total Mois'      => number_format($mois['value'],0, '.', ' ') . ' F',
                ],
                'ProductData' => $ventesParProduit,
                'OrderData'   => $ventesSemaine,

                'Commandes' => Order::with(['items.product'])
                                ->whereBetween('created_at', [
                                    Carbon::now()->startOfDay(),
                                    Carbon::now()->endOfDay(),
                                ])
                                ->get(),

                'meilleursVendeurs' => $meilleursVendeurs,
                'VendeursData' => $venteParUser,
            ];
        }




    /**
     * Nom affiché en header.
     */
    public function name(): ?string
    {
        return 'Tableau de bord';
    }

    /**
     * Description affichée sous le titre.
     */
    public function description(): ?string
    {
        return "Bienvenue sur l'application Misericorde Alu.";
    }

    /**
     * Boutons de commande (aucun ici).
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * Layout des éléments de l'écran.
     */
    public function layout(): iterable
    {
        return [

            // Première ligne : métriques classiques
            Layout::columns([
                Layout::metrics([
                    'Meilleur Vente'  => 'metrics.Meilleur Vente',
                    'Meilleur Client' => 'metrics.Meilleur Client',
                    'Ventes du Jour'  => 'metrics.Ventes du Jour',
                    'Total du jour'   => 'metrics.Total du jour',
                    'Total Mois'      => 'metrics.Total Mois',
                ]),
            ]),

            Layout::columns([
                OrderChart::class,
                ProductChart::class,
            ]),

            
            layout::columns([
                MeilleursVendeursLayout::class,
                UserSellingChart::class,
            ]),

            Layout::table('Commandes', [
                        // TD::make('name', 'Boutique')
                        //     ->render(fn($user) => $user->shop->name ?? 'Aucune'),

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
                                    default => 'text-muted'
                                };
                                return "<span class='{$color}'>{$order->status}</span>";
                            }),
                        ]),

            
            
        ];
    }

}
