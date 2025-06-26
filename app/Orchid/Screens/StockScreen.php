<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use App\Orchid\Layouts\StockLayout;
use App\Orchid\Filters\ShopFilter;
use App\Models\Product;
use App\Models\Shop;
use Orchid\Support\Facades\Layout;
use App\Orchid\Layouts\ShopFilterLayout;


class StockScreen extends Screen
{

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
        {
            return [
                'products' => Product::filters(ShopFilterLayout::class)
                    ->with('category', 'stockMovements')
                    ->whereHas('stockMovements', function ($query) {
                        $query->where('type', 'entry');
                    })
                    ->get(),
            ];
        }


    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Stock';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }


    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            ShopFilterLayout::class,
            StockLayout::class,
        ];
    }
}
