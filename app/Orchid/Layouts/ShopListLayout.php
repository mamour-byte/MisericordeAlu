<?php

namespace App\Orchid\Layouts;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use App\Models\Shop;
use Orchid\Screen\Actions\Link;


class ShopListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'shops';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('name', 'Nom')
                ->render(fn ($shop) => $shop->name),

            TD::make('location', 'Adresse')
                ->render(fn ($shop) => $shop->location),

            TD::make('manager.name', 'Gérant')
                ->render(fn ($shop) => $shop->manager->name),


            TD::make('created_at', 'Date de création')
                ->render(fn(Shop $shop) => $shop->created_at->format('d/m/Y')),

            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn(Shop $shop) => Link::make('Modifier')
                    ->route('platform.shop.edit', $shop->id)
                    ->icon('pencil')),

            
        ];
    }
}
