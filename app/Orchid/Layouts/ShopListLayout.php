<?php

namespace App\Orchid\Layouts;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use App\Models\Shop;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;


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
                ->render(fn (Shop $shop) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([

                        Link::make(__('Edit'))
                            ->icon('bs.pencil')
                            ->route('platform.shop.edit', $shop->id),

                        Button::make(__('Delete'))
                            ->icon('bs.trash3')
                            ->confirm(__('Once the account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.'))
                            
                    ])),

                

            
        ];
    }
}
