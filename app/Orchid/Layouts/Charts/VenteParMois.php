<?php

namespace App\Orchid\Layouts\Charts;

use Orchid\Screen\Layouts\Chart;


class VenteParMois extends Chart
{
    /**
     * Available options:
     * 'bar', 'line',
     * 'pie', 'percentage'.
     *
     * @var string
     */
    protected $type = 'bar';

    /**
     * Determines whether to display the export button.
     *
     * @var bool
     */
    protected $export = true;
    protected $title = 'Ventes Par Mois';
    protected $target = 'VentesParMois';

    /**
     * The chart data.
     *
     * @var array
     */

    protected $data = [
        'labels' => [
            'Janvier', 'Février', 'Mars', 'Avril', 'Mai',  
            'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre',  
            'Novembre', 'Décembre',
        ],
    ];


}
