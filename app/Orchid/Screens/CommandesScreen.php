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
use App\Http\Controllers\OrderController;


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

                Group::make([
                    Relation::make('order.products')   // Utilisation de Relation pour sélectionner des produits
                        ->title('Produits')
                        ->fromModel(Product::class, 'name')  // Associer les produits à la commande
                        ->multiple()
                        ->required()
                        ->help('Sélectionnez les produits (Ctrl+clic pour multiple)'),

                    Input::make('order.quantities')  // Les quantités pour chaque produit
                        ->title('Quantités')
                        ->type('text')
                        ->required()
                        ->help('Format: 1,2,3 (une quantité par produit)')
                ]),
            ])
        ];
    }

    public function save(Request $request)
    {
        // Logique de sauvegarde de la commande avec produits et quantités
        return app(OrderController::class)->save($request);
    }
}
