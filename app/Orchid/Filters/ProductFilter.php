<?php

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Input;

class ProductFilter extends Filter
{
    /**
     * The displayable name of the filter.
     */
    public function name(): string
    {
        return 'Rechercher';
    }

    /**
     * The array of matched parameters.
     */
    public function parameters(): ?array
    {
        return ['q'];
    }

    /**
     * Apply to a given Eloquent query builder.
     */
    public function run(Builder $builder): Builder
    {
        if ($this->request->filled('q')) {
            $like = '%' . $this->request->get('q') . '%';
            $builder->where(function ($query) use ($like) {
                $query->where('name', 'like', $like)
                    ->orWhere('description', 'like', $like);
            });
        }

        return $builder;
    }

    /**
     * Get the display fields.
     */
    public function display(): iterable
    {
        return [
            Input::make('q')
                ->type('text')
                ->title('Recherche')
                ->placeholder('Nom ou description du produit...')
                ->value($this->request->get('q')),
        ];
    }
}
