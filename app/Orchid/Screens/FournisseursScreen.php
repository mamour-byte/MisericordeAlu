<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use App\Models\Supplier;
use App\Orchid\Layouts\FournisseurLayout;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\ModalToggle;

class FournisseursScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        {
            return [
                'suppliers' => Supplier::latest()->paginate(10),
            ];
        }
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Fournisseurs';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('Ajouter un fournisseur'))
                ->icon('bs.plus-circle')
                ->route('platform.Fournisseurs.addFournisseurs'),

            // Link::make(__('Bon de commande'))
            //     ->icon('bs.plus-circle'),
            //     ->route('platform.Fournisseurs.addBonCommande'),


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
            FournisseurLayout::class,
        ];
    }
}
