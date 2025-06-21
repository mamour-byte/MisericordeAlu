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
            TD::make('name', 'Vendeur')->render(function ($user) {
                $initiales = collect(explode(' ', $user->name))
                    ->map(fn($part) => strtoupper(mb_substr($part, 0, 1)))
                    ->join('');

                    return <<<HTML
                        <div style="display: flex; align-items: center;">
                            <div style="
                                width: 35px;
                                height: 35px;
                                border-radius: 50%;
                                background-color:rgb(52, 139, 91);
                                color: white;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                font-weight: bold;
                                margin-right: 10px;
                            ">
                                {$initiales}
                            </div>
                            <span>{$user->name}</span>
                        </div>
                    HTML;
                }),

            TD::make('name', 'Boutique')
                ->render(fn($user) => $user->shop->name ?? 'Aucune'),

            TD::make('total_commandes', 'Commandes'),

            TD::make('total_ventes', 'Montant total')->render(fn($user) =>
                number_format($user->total_ventes, 0, ',', ' ') . ' F CFA'),
        ];
    }
}
