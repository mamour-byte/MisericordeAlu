<?php

namespace App\Orchid\Layouts\Charts;

use Orchid\Screen\Layouts\Chart;

class OrderChart extends Chart
{
    /**
     * Available options:
     * 'bar', 'line',
     * 'pie', 'percentage'.
     *
     * @var string
     */
    protected $type = 'line';

    /**
     * Determines whether to display the export button.
     *
     * @var bool
     */
    protected $export = true;
        /**
     * Configuring line.
     *
     * @var array
     */
    protected $lineOptions = [
        'spline'     => 1,
        'regionFill' => 1,
        'hideDots'   => 0,
        'hideLine'   => 0,
        'heatline'   => 0,
        'dotSize'    => 3,
    ];
    /**
     * The name of the data source.
     *
     * @var string
     */
    protected $target = 'OrderData';
    /**
     * The chart title.
     *
     * @var string
     */
    protected $title = 'Ventes de la semaine';
}
