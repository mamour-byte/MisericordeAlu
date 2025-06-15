<?php

namespace App\Orchid\Screens\Crud;

use App\Models\Shop;
use App\Models\User;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Alert;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;

class EditShopScreen extends Screen
{
    public $shop;

    /**
     * Query data.
     */
    public function query(Shop $shop): iterable
    {
        $this->shop = $shop;

        return [
            'shop' => $shop
        ];
    }

    /**
     * Screen name.
     */
    public function name(): ?string
    {
        return 'Modifier la Boutique';
    }

    /**
     * Command bar.
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
     * Layouts.
     */
    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('shop.name')
                    ->title('Nom de la boutique')
                    ->required(),

                Input::make('shop.location')
                    ->title('Adresse'),

                Relation::make('shop.manager_id')
                    ->fromModel(User::class, 'name')
                    ->title('Gérant')
                    ->required(),
            ]),
        ];
    }

    /**
     * Save handler.
     */
    public function save(Request $request, Shop $shop)
    {
        $validated = $request->validate([
            'shop.name' => 'required|string|max:255',
            'shop.location' => 'nullable|string|max:255',
            'shop.manager_id' => 'required|exists:users,id',
        ]);

        $shop->fill($validated['shop'])->save();

        Alert::info('Boutique mise à jour avec succès.');

        return redirect()->route('platform.shop'); // adapte à ton nom de route
    }
}
