<?php

namespace App\Orchid\Layouts;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use App\Models\Product;
use App\Models\User;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Button;

class ProductListLayout extends Table
{
    protected $target = 'products';

    protected function columns(): iterable
    {
        return [

            TD::make('name', 'Nom')
                ->render(fn ($product) => $product->name),

            TD::make('description', 'Description')
                ->render(fn ($product) => $product->description),

            TD::make('price', 'Prix')
                ->render(fn ($product) => number_format($product->price, 2) . ' FCFA')
                ->sort(),

            TD::make('subcategory.name', 'Sous-catégorie')
                ->render(fn ($product) => optional($product->subcategory)->name ?? '—')
                ->sort(),

            TD::make('stock_quantity', 'Quantité en stock')
                ->render(fn ($product) => $product->stock_quantity)
                ->sort(),

            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(function (Product $product) {
                    return DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
                            Link::make(__('Modifier'))
                                ->route('platform.Product.edit', $product->id)
                                ->icon('bs.pencil'),
            
                            Button::make(__('Supprimer'))
                                ->icon('bs.trash3')
                                ->confirm(__('Cette action est irréversible.'))
                                ->method('delete', [
                                    'id' => $product->id,
                                ]),
                        ]);
                }),
            
            
        ];
    }
}
