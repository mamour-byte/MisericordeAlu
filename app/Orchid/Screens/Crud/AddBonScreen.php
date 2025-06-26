<?php

namespace App\Orchid\Screens\Crud;

use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Matrix;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;
use App\Models\Supplier;
use Orchid\Screen\Actions\Button;

class AddBonScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Bon de Commande';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::rows([
                Relation::make('supplier.name')
                    ->title('Fournisseur')
                    ->fromModel(Supplier::class, 'name')
                    ->required(),

                Group::make([
                    Input::make('supplier.email')
                        ->title('Email')
                        ->type('email')
                        ->required(),

                    Input::make('supplier.address')
                        ->title('Adresse'),
                ]),

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
                            'door'   => 'Porte',
                            'window' => 'Fenêtre',
                        ])->required(),

                        'width'        => Input::make()->type('number')->min(1)->required(),
                        'height'       => Input::make()->type('number')->min(1)->required(),
                        'price_meter'  => Input::make()->type('number')->required(),
                        'quantity'     => Input::make()->type('number')->min(1)->required(),
                        'note'         => Input::make()->type('text'),
                        ]),

                


            ])
        ];
    }
}
