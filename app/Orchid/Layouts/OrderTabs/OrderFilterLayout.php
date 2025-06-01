<?php

namespace App\Orchid\Layouts\OrderTabs;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use App\Orchid\Filters\CommandesFilter;


class OrderFilterLayout extends Table
{
    /**
     * @return string[]|Filter[]
     */
    public function filters(): array
    {
        return [
            CommandesFilter::class,
        ];
    }
}
