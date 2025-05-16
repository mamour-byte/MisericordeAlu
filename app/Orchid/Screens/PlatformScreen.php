<?php

declare(strict_types=1);

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use App\Http\Controllers\ChartController;
use App\Orchid\Layouts\Charts\ProductChart;
use App\Orchid\Layouts\Charts\OrderChart;
use App\Orchid\Layouts\Charts\MeilleursVendeursLayout;
use App\Orchid\Layouts\OrderTabs\OrderLayout;
use App\Models\Order;

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


            return [
                'metrics' => [
                    'Meilleur Vente'  => $chart->MeilleurVenteMois(),
                    'Meilleur Client' => $chart->MeilleurClientMois(),
                    'Ventes du Jour'  => $chart->VentesJour(),
                    'Total Semaine'   => $chart->TotalSemaine(),
                    'Total Mois'      => $chart->TotalMois(),
                ],
                'ProductData' => $ventesParProduit,
                'OrderData'   => $ventesSemaine,

                'Commandes' => Order::with(['items.product'])->latest()->paginate(10),
                'meilleursVendeurs' => $meilleursVendeurs,
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
                    'Total Semaine'   => 'metrics.Total Semaine',
                    'Total Mois'      => 'metrics.Total Mois',
                ]),
            ]),

            Layout::columns([
                OrderChart::class,
                ProductChart::class,
            ]),

            layout::columns([
                MeilleursVendeursLayout::class,
            ]),

            
            
        ];
    }

}
