<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use App\Models\Product;
use App\Orchid\Layouts\ProductListLayout;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\DropDown;


class ProductScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
        {
            return [
                'products' => Product::with('stockMovements')->paginate(15),
            ];
        }


    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Produits';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('Add'))
                ->icon('bs.plus-circle')
                ->route('platform.Product.add'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            ProductListLayout::class,
        ];
    }

    /**
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove(Product $product)
    {
        $product->delete();

        return redirect()->route('platform.Product')
                ->with('success', 'Produit supprimé avec succès');
    }
}
