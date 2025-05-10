<?php

namespace App\Orchid\Screens\Crud;

use App\Models\Supplier;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Input; 
use Orchid\Screen\Fields\TextArea;
use Orchid\Support\Facades\Alert;
use Orchid\Screen\Actions\Button;
use Illuminate\Http\Request;

class EditFournisseursScreen extends Screen
{
    public function query(Supplier $supplier): iterable
    {
        return [
            'supplier' => $supplier,
        ];
    }

    public function name(): ?string
    {
        return 'Modifier un fournisseur';
    }

    public function commandBar(): iterable
    {
        return [
            Button::make('Modifier')
                ->icon('check')
                ->method('save'),
        ];
    }


    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('supplier.id')
                    ->type('hidden'),

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

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(Request $request)
    {
        $data = $request->get('supplier');

        $supplier = Supplier::findOrFail($data['id']);
        $supplier->update($data);

        Alert::info('Fournisseur mis à jour avec succès.');

        return redirect()->route('platform.Fournisseurs');
    }
}
