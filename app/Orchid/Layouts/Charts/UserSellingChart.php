<?php

namespace App\Orchid\Layouts\Charts;

use Orchid\Screen\Layouts\Chart;

class UserSellingChart extends Chart
{
    /**
     * Available options:
     * 'bar', 'line',
     * 'pie', 'percentage'.
     *
     * @var string
     */
    protected $type = 'pie';

    /**
     * Determines whether to display the export button.
     *
     * @var bool
     */
    protected $export = true;
    /**
     * Chart data.
     *
     * @var string
     */
    protected $target = 'VendeursData';
    protected $title = 'Vendeur ';
}
