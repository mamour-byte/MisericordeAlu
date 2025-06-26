<?php

namespace App\Orchid\Layouts;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use App\Orchid\Filters\ShopFilter;
use Orchid\Screen\Layouts\Selection;

class ShopFilterLayout extends Selection
{
    public function filters(): array
    {
        return [
            ShopFilter::class,
        ];
    }
}
