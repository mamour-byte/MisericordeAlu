<?php

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Select;
use App\Models\Shop;

class ShopFilter extends Filter
{
    public function name(): string
    {
        return 'Boutique';
    }

    public function parameters(): array
    {
        return ['shop_id'];
    }

    public function run(Builder $builder): Builder
    {
        if ($shopId = $this->request->get('shop_id')) {
            if (Shop::where('id', $shopId)->exists()) {
                $builder->where('shop_id', $shopId);
            }
        }
        return $builder;
    }

    public function display(): iterable
    {
        
        return [
            Select::make('shop_id')
                ->fromModel(Shop::class, 'name')
                ->title('Boutique')
                ->empty('Toutes les boutiques')
                ->value($this->request->get('shop_id')),
        ];
    }

    /**
     * Value to be displayed
     */
    public function value(): string
        {
            return $this->request->get('shop_id')
                ? Shop::find($this->request->get('shop_id'))?->name ?? 'Boutique inconnue'
                : 'Toutes les boutiques';
        }
}
