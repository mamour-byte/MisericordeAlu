<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use App\Models\Product;
use App\Orchid\Layouts\ProductListLayout;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\DropDown;
use Orchid\Support\Facades\Toast;
use PDF;




class ProductScreen extends Screen
{

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
        {
            $shop = auth()->user()->shop;
            $user = auth()->user();
            if (!$user->shop) {
                Toast::error("Aucun magasin ne vous a été attribué. Veuillez contacter l'administrateur.");
                return [
                    'Commandes' => collect(), 
                ];
            }
            return [
                'products' => Product::with('stockMovements')
                    ->where('shop_id', $shop->id)
                    ->paginate(15),
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
            Link::make(__('Ajouter'))
                ->icon('bs.plus-circle')
                ->route('platform.Product.add'),

            Link::make('Bon de commande auto')
                ->icon('bs.download')
                ->route('products.export.lowstock'),
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
