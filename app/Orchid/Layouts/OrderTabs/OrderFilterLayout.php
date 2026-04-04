<?php

namespace App\Orchid\Layouts\OrderTabs;

use Orchid\Screen\Layouts\Selection;
use App\Orchid\Filters\CommandesFilter;


class OrderFilterLayout extends Selection
{
    /**
     * Retourne la liste des filtres Orchid
     */
    public function filters(): iterable
    {
        return [
            CommandesFilter::class,
        ];
    }
}
