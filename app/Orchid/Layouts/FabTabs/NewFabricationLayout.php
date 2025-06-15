<?php

namespace App\Orchid\Layouts\FabTabs;

use Orchid\Screen\Field;
use Orchid\Screen\Layouts\Rows;

use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Matrix;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;


class NewFabricationLayout extends Rows
{
    /**
     * Used to create the title of a group of form elements.
     *
     * @var string|null
     */
    protected $title;

    /**
     * Get the fields elements to be displayed.
     *
     * @return Field[]
     */
    protected function fields(): iterable
    {
        return [

                Group::make([
                    Input::make('order.customer_name')
                        ->title('Nom du client')
                        ->placeholder('Entrez le nom du client')
                        ->required(),

                    Input::make('order.customer_phone')
                        ->title('Téléphone')
                        ->placeholder('Entrez le numéro de téléphone')
                        ->required(),
                ]),

                Group::make([
                    Input::make('order.customer_email')
                        ->title('Email')
                        ->placeholder('Entrez l\'email du client'),

                    Input::make('order.customer_address')
                        ->title('Adresse')
                        ->placeholder('Entrez l\'adresse du client'),
                ]),

                // Liste des produits avec dimensions via Matrix
                Matrix::make('items')
                    ->title('Articles (Portes / Fenêtres)')
                    ->columns([
                        'Type'         => 'type',
                        'Largeur'      => 'width',
                        'Hauteur'      => 'height',
                        'Prix m²'      => 'price_meter',
                        'Quantité'     => 'quantity',
                        'Note'         => 'note',
                    ])
                    ->fields([
                        'type'         => Select::make()->options([
                            'Porte'   => 'Porte',
                            'Fenêtre' => 'Fenêtre',
                        ])->required(),

                        'width'        => Input::make()->type('number')->min(1)->required(),
                        'height'       => Input::make()->type('number')->min(1)->required(),
                        'price_meter'  => Input::make()->type('number')->required(),
                        'quantity'     => Input::make()->type('number')->min(1)->required(),
                        'note'         => Input::make()->type('text'),
                        ]),

                Select::make('order.docs')
                    ->title('Statut')
                    ->options([
                        'quote'   => 'Devis',
                        'invoice' => 'Facture',
                    ])
                    ->empty('Sélectionnez un statut')
                    ->required(),

                Button::make('Enregistrer la commande')
                    ->method('save')
                    ->class('btn btn-primary'),
            
        ];
    }
}
