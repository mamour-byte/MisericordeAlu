<?php

namespace App\Orchid\Layouts\Charts;

use Orchid\Screen\Layouts\Chart;

class ProductChart extends Chart
{
    /**
     * Chart data.
     *
     * @var array
     */
    protected $type = 'bar';

    /**
     * Chart options.
     *
     * @var array
     */
    protected $export = true;

    protected $target = 'ProductData';


    protected $title = 'Répartition des ventes par produit';
}
