<?php

namespace App\Orchid\Screens\Crud;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Alert;

class EditProductScreen extends Screen
{
    /**
     * Query data.
     *
     * @param Product $product
     * @return iterable
     */
    public function query(Product $product): iterable
    {
        return [
            'product' => $product,
        ];
    }

    
    public function name(): ?string
    {
        return 'Modifier le produit';
    }

    /**
     * Button commands.
     *
     * @return iterable
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Modifier')
                ->icon('check')
                ->method('save'),
        ];
    }

    /**
     * Views.
     *
     * @return iterable
     */
    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('product.id') 
                    ->type('hidden'),

                Input::make('product.name')
                    ->title('Nom du produit')
                    ->required(),

                TextArea::make('product.description')
                    ->title('Description'),

                Input::make('product.price')
                    ->title('Prix')
                    ->type('number')
                    ->required(),

                Input::make('product.stock_quantity')
                    ->title('Quantité en stock')
                    ->type('number')
                    ->required(),

                Input::make('product.stock_min')
                    ->title("Seuil d'alerte")
                    ->type('number')
                    ->required(),

                Relation::make('product.subcategory_id')
                    ->title('Sous-catégorie')
                    ->fromModel(Category::class, 'name')
                    ->required(),
            ]),
        ];
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(Request $request)
    {
        $data = $request->get('product');

        // On retrouve le bon produit par ID
        $product = Product::findOrFail($data['id']);

        $product->update($data);

        Alert::info('Produit mis à jour avec succès.');

        return redirect()->route('platform.Product');
    }
}
