<?php

namespace App\Orchid\Layouts;

use App\Orchid\Filters\ProductFilter;
use Orchid\Screen\Layouts\Selection;

class ProductFilterLayout extends Selection
{
    /**
     * @return string[]|\Orchid\Filters\Filter[]
     */
    public function filters(): array
    {
        return [
            ProductFilter::class,
        ];
    }
}
