<?php

namespace App\Orchid\Layouts\OrderTabs;

use Orchid\Screen\Layouts\Rows;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Relation;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;


class NewOrderLayout extends Rows
{
    
    protected function fields(): array 
    {
        return [

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
                    Relation::make('order.products')  
                        ->title('Produits')
                        ->fromModel(Product::class, 'name') 
                        ->multiple()
                        ->required()
                        ->help('Sélectionnez les produits (Ctrl+clic pour multiple)'),

                    Input::make('order.quantities') 
                        ->title('Quantités')
                        ->type('text')
                        ->required()
                        ->help('Format: 1,2,3 (une quantité par produit)'),
                     ]),

                     Button::make('Nouvelle Vente')
                        ->method('save')
                        ->confirm('Confirmez l\'ajout au tableau?')
                        ->class('btn btn-primary'),
        ];
    }
}
