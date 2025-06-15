<?php

namespace App\Orchid\Screens\Crud;

use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use App\Models\Fabrication;
use App\Models\FabricationItem;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Matrix;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Actions\Button;


class FabricationEditScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Fabrication $fabrication): iterable
        {
            $fabrication->load('items');

            return [
                'Fabrication' => $fabrication,
                'items' => $fabrication->items->map(function ($item) {
                    return [
                        'type'        => $item->type,
                        'width'       => $item->width,
                        'height'      => $item->height,
                        'price_meter' => $item->price_meter,
                        'quantity'    => $item->quantity,
                        'note'        => $item->note,
                    ];
                })->toArray(),
            ];
        }


    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'FabricationEditScreen';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Modifier')
                ->icon('update')
                ->method('updateFab'),
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
            Layout::rows([

                Input::make('Fabrication.id')
                        ->type('hidden'),

                Group::make([

                    Input::make('Fabrication.customer_name')
                        ->title('Nom du client')
                        ->placeholder('Entrez le nom du client')
                        ->required(),

                    Input::make('Fabrication.customer_phone')
                        ->title('Téléphone')
                        ->placeholder('Entrez le numéro de téléphone')
                        ->required(),
                ]),

                Group::make([
                    Input::make('Fabrication.customer_email')
                        ->title('Email')
                        ->placeholder('Entrez l\'email du client'),

                    Input::make('Fabrication.customer_address')
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
                            'door'   => 'Porte',
                            'window' => 'Fenêtre',
                        ])->required(),

                        'width'        => Input::make()->type('number')->min(1)->required(),
                        'height'       => Input::make()->type('number')->min(1)->required(),
                        'price_meter'  => Input::make()->type('number')->required(),
                        'quantity'     => Input::make()->type('number')->min(1)->required(),
                        'note'         => Input::make()->type('text'),
                        ]),

                Select::make('Fabrication.docs')
                    ->title('Statut')
                    ->options([
                        'quote'   => 'Devis',
                        'invoice' => 'Facture',
                    ])
                    ->empty('Sélectionnez un statut')
                    ->required(),

            ]),
        ];
    }

    /**
     * Handle the update of the fabrication.
     *
     * @param Fabrication $Fabrication
     * @return \Illuminate\Http\RedirectResponse
     */

    public function updateFab(Fabrication $fabrication)
    {
        $data = request()->get('Fabrication');

        // Validation des données
        $this->validate(request(), [
            'Fabrication.customer_name' => 'required|string|max:255',
            'Fabrication.customer_phone' => 'required|string|max:20',
            'Fabrication.docs' => 'required|in:quote,invoice',
        ]);

        // Mise à jour de la fabrication
        $fabrication->update([
            'customer_name'    => $data['customer_name'],
            'customer_phone'   => $data['customer_phone'],
            'customer_email'   => $data['customer_email'] ?? null,
            'customer_address' => $data['customer_address'] ?? null,
            'status'           => $data['docs'],
        ]);

        // Mise à jour des items
        foreach (request()->get('items', []) as $itemData) {
            FabricationItem::updateOrCreate(
                ['fabrication_id' => $fabrication->id, 'type' => $itemData['type']],
                [
                    'width'       => $itemData['width'],
                    'height'      => $itemData['height'],
                    'price_meter' => $itemData['price_meter'],
                    'quantity'    => $itemData['quantity'],
                    'note'        => $itemData['note'] ?? null,
                ]
            );
        }

        return redirect()->route('platform.Fabrication')
                        ->with('success', 'La fabrication a été mise à jour avec succès.');
    }
}
