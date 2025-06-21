<?php

namespace App\Orchid\Layouts;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use App\Models\Product;
use App\Models\User;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Button;
use Illuminate\Support\Facades\Auth;

class ProductListLayout extends Table
    {
    /**
     * Message personnalisé lorsqu'il n'y a aucun produit.
     */
    protected $empty = 'Aucun produit enregistré pour le moment.';
    
    protected $target = 'products';

    protected function columns(): iterable
    {

        return [
            
            TD::make('name', 'Nom')
                ->render(fn ($product) => $product->name),

            TD::make('description', 'Description')
                ->render(fn ($product) => $product->description),

            TD::make('price', 'Prix')
                ->render(fn ($product) => number_format($product->price, 2) . ' FCFA')
                ->sort(),

            TD::make('categories.name', 'Catégorie')
                ->render(fn ($product) => optional($product->categorie)->name ?? '—')
                ->sort(),


            TD::make('stockMovements', 'Ventes / Stock')
                ->render(function (Product $product) {
                    $user = Auth::user();
                    $shop = $user->shop;

                    if (!$shop) {
                        return '<div class="text-muted">Pas de boutique liée</div>';
                    }

                    $entries = $product->stockMovements
                        ->where('shop_id', $shop->id)
                        ->where('type', 'entry');

                    $exits = $product->stockMovements
                        ->where('shop_id', $shop->id)
                        ->where('type', 'exit');

                    $totalEntree = $entries->sum('quantity');
                    $totalSortie = $exits->sum('quantity');

                    if ($totalEntree === 0) {
                        return '<div class="text-muted">Aucun stock enregistré</div>';
                    }

                    $pourcentage = round(($totalSortie / $totalEntree) * 100);

                    $couleur = 'bg-success';
                    if ($pourcentage >= 95) {
                        $couleur = 'bg-danger';
                    } elseif ($pourcentage >= 80) {
                        $couleur = 'bg-warning';
                    }

                    $bar = <<<HTML
                        <div>
                            <div class="small mb-1">
                                Vendu : {$totalSortie} / {$totalEntree} 
                                <span class="float-right">{$pourcentage}%</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar {$couleur}" role="progressbar" style="width: {$pourcentage}%;" aria-valuenow="{$pourcentage}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    HTML;

                    return $bar;
                })
                ->width('250px')
                ->cantHide(),


            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(function (Product $product) {
                    return DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
                            Link::make(__('Modifier'))
                                ->route('platform.Product.edit', $product->id)
                                ->icon('bs.pencil'),
            
                            Button::make(__('Supprimer'))
                                ->icon('bs.trash3')
                                ->confirm(__('Cette action est irréversible.'))
                                ->method('delete', [
                                    'id' => $product->id,
                                ]),
                        ]);
                }),
            
            
        ];
    }
}
