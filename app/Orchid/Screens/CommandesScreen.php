<?php

namespace App\Orchid\Screens;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Group;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Relation;
use Orchid\Support\Facades\Alert;
use App\Models\Category;
use App\Models\Subcategory;


class CommandesScreen extends Screen
{
    public function query(): iterable
    {
        return [];
    }

    public function name(): ?string
    {
        return 'Créer une commande';
    }

    public function commandBar(): iterable
    {
        return [
            Button::make('Enregistrer')
                ->method('save')
                ->icon('check'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::rows([
                Group::make([
                    Input::make('order.customer_name')
                        ->title('Nom du client')
                        ->placeholder('Entrez le nom du client')
                        ->required(),

                    Input::make('order.customer_email')
                        ->title('Email')
                        ->placeholder('Entrez l\'email du client'),
                ]),
                Group::make([
                    Input::make('order.customer_phone')
                        ->title('Téléphone'),

                    Input::make('order.customer_address')
                        ->title('Adresse'),
                    ]),

                Relation::make('order.product_id')
                    ->title('Produit')
                    ->fromModel(Product::class, 'name')
                    ->required(),
            
                Input::make('order.quantity')
                    ->title('Quantité')
                    ->type('number')
                    ->required(),
            
            ])
            
        ];
    }

    public function save(Request $request)
    {
        $data = $request->get('order');

        $total = $data['quantity'] * $data['price'];

        // Création de la commande principale
        $order = Order::create([
            'customer_name' => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'customer_phone' => $data['customer_phone'],
            'customer_address' => $data['customer_address'],
            'status' => 'pending',
            'total_amount' => $total,
        ]);

        // Création de l'item associé
        $order->items()->create([
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
            'price' => $data['price'],
        ]);

        Alert::info('Commande enregistrée avec succès.');

        return redirect()->route('platform.Commandes');
    }

}
