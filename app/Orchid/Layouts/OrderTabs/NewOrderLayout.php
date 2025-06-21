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
use Orchid\Screen\Fields\Label;
use Orchid\Support\Facades\Alert;


class NewOrderLayout extends Rows
{
    
    protected function fields(): array 
    {   
        $user = auth()->user();
        if (!$user->shop) {
            return [
                Label::make()
                    ->value('Aucun magasin ne vous a été attribué. Veuillez contacter l\'administrateur.')
                    ->title('Erreur')
            ];
        }
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
                        ->applyScope('byShop', auth()->user()->shop?->id)
                        ->searchColumns('name') 
                        ->displayAppend('name') 
                        ->multiple()
                        ->required(),

                    Input::make('order.quantities') 
                        ->title('Quantités')
                        ->type('text')
                        ->required()
                        ->help('Format: 1,2,3 (une quantité par produit)'),
                     ]),

                    Select::make('order.Docs')
                        ->title('Statut')
                        ->options([
                            'Quote'   => 'Devis',
                            'Invoice' => 'Facture',
                        ])
                        ->empty('Sélectionnez un statut'),

                    Button::make('Nouvelle Vente')
                        ->method('save')
                        ->confirm('Confirmez l\'ajout au tableau?')
                        ->class('btn btn-primary'),
        ];
    }
}
