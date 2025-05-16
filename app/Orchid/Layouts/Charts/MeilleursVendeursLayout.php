<?php

namespace App\Orchid\Layouts\Charts;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class MeilleursVendeursLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'meilleursVendeurs';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('name', 'Nom du vendeur'),
            TD::make('total_commandes', 'Commandes'),
            TD::make('total_ventes', 'Montant total')->render(fn($user) =>
                number_format($user->total_ventes, 0, ',', ' ') . ' F CFA'),
        ];
    }
}
