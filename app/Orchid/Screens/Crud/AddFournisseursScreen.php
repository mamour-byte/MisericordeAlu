<?php

namespace App\Orchid\Screens\Crud;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Support\Facades\Alert;
use Orchid\Screen\Actions\Button;

class AddFournisseursScreen extends Screen
{
    public function query(): iterable
    {
        return [];
    }

    public function name(): ?string
    {
        return 'Ajouter un fournisseur';
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
                Input::make('supplier.name')
                    ->title('Nom du fournisseur')
                    ->required(),

                Input::make('supplier.email')
                    ->title('Email'),

                Input::make('supplier.phone')
                    ->title('Contact')
                    ->required(),

            ])
        ];
    }

    public function save(Request $request)
    {
        $data = $request->get('supplier');

        Supplier::create($data);

        Alert::info('Fournisseur ajouté avec succès.');

        return redirect()->route('platform.Fournisseurs');
    }
}
