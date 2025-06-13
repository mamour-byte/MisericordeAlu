<?php

namespace App\Orchid\Screens\Crud;

use App\Models\Shop;
use App\Models\User;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Alert;
use Illuminate\Http\Request;

class AddShopScreen extends Screen
{
    public function query(): iterable
    {
        return [];
    }

    public function name(): ?string
    {
        return 'Créer une Boutique';
    }

    public function commandBar(): iterable
    {
        return [
            \Orchid\Screen\Actions\Button::make('Enregistrer')
                ->method('createOrUpdate')
                ->icon('check'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('shop.name')
                    ->title('Nom de la boutique')
                    ->placeholder('Ex: Boutique Médina')
                    ->required(),

                Input::make('shop.location')
                    ->title('Emplacement')
                    ->placeholder('Ex: Dakar, Médina'),

                Select::make('shop.manager_id')
                    ->fromModel(User::class, 'name')
                    ->title('Gérant')
                    ->empty('Sélectionner un gérant', 0)
                    ->required(),
            ])
        ];
    }

    public function createOrUpdate(Request $request)
    {
        $data = $request->get('shop');

        $request->validate([
            'shop.name' => 'required|string|max:255',
            'shop.location' => 'nullable|string|max:255',
            'shop.manager_id' => 'required|exists:users,id',
        ]);

        $shop = new Shop();
        $shop->name = $data['name'];
        $shop->location = $data['location'] ?? null;
        $shop->manager_id = $data['manager_id'];
        $shop->save();


        Alert::info('Boutique créée avec succès.');

        return redirect()->route('platform.shop'); // adapte selon ta route
    }
}
