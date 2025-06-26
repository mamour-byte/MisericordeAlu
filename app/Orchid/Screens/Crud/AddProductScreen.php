<?php

namespace App\Orchid\Screens\Crud;

use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Orchid\Support\Facades\Alert;
use App\Models\StockMovement;



class AddProductScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [];
    }

    /**
     * Button commands.
     *
     * @return iterable
     */
    public function name(): ?string
    {
        return 'Ajouter un Produit';
    }


    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Enregistrer')
                ->icon('check')
                ->method('save'),
        ];
    }

    
    /**
     * Button Layout.
     *
     * @return iterable
     * @throws \Exception
     */
    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('product.name')
                    ->title('Nom du produit')
                    ->required(),

                TextArea::make('product.description')
                    ->title('Description')
                    ->rows(3),

                Input::make('product.price')
                    ->title('Prix')
                    ->step(0.01)
                    ->required(),

                Input::make('product.stock_quantity')
                    ->title('Quantité en stock')
                    ->required(),

                Input::make('product.stock_min')
                    ->title("Seuil d'alerte")
                    ->required(),

                Relation::make('product.categorie_id')
                    ->title('Catégorie')
                    ->fromModel(Category::class, 'name')
                    ->required(),
            ])
        ];
    }


    public function save(Request $request)
        {
        $data = $request->get('product');
        $shop = auth()->user()->shop;

        // Ajoute shop_id au tableau de données
        $data['shop_id'] = $shop->id;

        $product = Product::create($data);

        StockMovement::create([
            'product_id' => $product->id,
            'order_id'  => null, 
            'type'       => StockMovement::TYPE_ENTRY,
            'quantity'   => $product->stock_quantity,
            'notes'      => 'Ajout initial du produit',
            'shop_id'   => $shop->id, 
        ]);

        Alert::success('Produit ajouté avec succès.');

        return redirect()->route('platform.Product');
    }

}
