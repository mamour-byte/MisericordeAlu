<?php

namespace App\Orchid\Layouts;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use App\Models\Product;
use App\Models\User;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Button;

class ProductListLayout extends Table
{
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

            TD::make('subcategory.name', 'Sous-catégorie')
                ->render(fn ($product) => optional($product->subcategory)->name ?? '—')
                ->sort(),

            TD::make('stockMovements', 'Ventes / Stock')
                ->render(function (Product $product) {
                    $totalEntree = $product->stockMovements->where('type', 'entry')->sum('quantity');
                    $totalSortie = $product->stockMovements->where('type', 'exit')->sum('quantity');

                    if ($totalEntree === 0) {
                        return '<div class="text-muted">Aucun stock enregistré</div>';
                    }

                    $pourcentage = round(($totalSortie / $totalEntree) * 100);

                    // Détermination de la couleur
                    $couleur = 'bg-success'; // vert
                    if ($pourcentage >= 95) {
                        $couleur = 'bg-danger'; // rouge
                    } elseif ($pourcentage >= 80) {
                        $couleur = 'bg-warning'; // orange
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
