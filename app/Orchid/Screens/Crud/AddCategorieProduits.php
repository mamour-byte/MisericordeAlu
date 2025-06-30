<?php

namespace App\Orchid\Screens\Crud;

use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Input;
use App\Models\Category;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Toast;

class AddCategorieProduits extends Screen
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
        return 'Catégorie de Produits';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
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
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('category.name')
                    ->title('Nom de la Catégorie')
                    ->placeholder('Entrez le nom de la catégorie')
                    ->required(),

                Input::make('category.description')
                    ->title('Description')
                    ->placeholder('Entrez une description pour la catégorie'),
                
            ]),
        ];
    }

    /**
     * Save the category.
     *
     * @param array $data
     * @return void
     */
    public function save()
        {
            $data = request()->get('category');

            $validated = validator($data, [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
            ])->validate();

            $category = new Category();
            $category->name = $validated['name'];
            $category->description = $validated['description'] ?? '';
            $category->save();

            Toast::info('Catégorie enregistrée avec succès.');
        }
}
