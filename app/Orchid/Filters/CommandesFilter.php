<?php

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Field;

class CommandesFilter extends Filter
{
    // Déclare le nom du paramètre attendu
    public $parameters = ['Commandes'];

    /**
     * Nom affiché du filtre (optionnel)
     */
    public function name(): string
    {
        return 'Filtrer par client';
    }

    /**
     * Retourne les paramètres utilisés par ce filtre
     */
    public function parameters(): ?array
    {
        return ['Commandes'];
    }

    /**
     * Applique le filtre à la requête
     */
    public function run(Builder $builder): Builder
    {
        // Vérifie si une valeur a été saisie, sinon ne filtre pas
        if ($this->request->filled('Commandes')) {
            return $builder->where('customer_name', 'like', '%' . $this->request->get('Commandes') . '%');
        }

        return $builder;
    }

    /**
     * Affiche le champ de filtre dans l’interface
     */
    public function display(): iterable
    {
        return [
            Input::make('Commandes')
                ->type('text')
                ->title('Nom du client')
                ->placeholder('Rechercher un client')
                ->value($this->request->get('order'))
                ->help('Filtre les commandes par nom du client.'),
        ];
    }
}
