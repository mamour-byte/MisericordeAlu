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

class PlatformScreen extends Screen
{
    /**
     * Récupère les données à afficher.
     *
     * @return array
     */
    public function query(): iterable
        {
            $chart = app(ChartController::class);
            $ventesParProduit = $chart->VentesParProduit();
            $ventesSemaine = $chart->VentesSemaine();
            $meilleursVendeurs = $chart->MeilleursVendeurs();
            $venteParUser= $chart->VentesParUser();


            return [
                'metrics' => [
                    'Meilleur Vente'  => $chart->MeilleurVenteMois(),
                    'Meilleur Client' => $chart->MeilleurClientMois(),
                    'Ventes du Jour'  => $chart->VentesJour(),
                    'Total Mois'      => $chart->TotalMois(),
                ],
                'ProductData' => $ventesParProduit,
                'OrderData'   => $ventesSemaine,

                'Commandes' => Order::with(['items.product'])
                                ->latest()
                                ->paginate(8),

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
                        TD::make('Date de création')
                            ->sort()
                            ->render(fn(Order $order) => $order->created_at),
                        ]),

            
            
        ];
    }

}
