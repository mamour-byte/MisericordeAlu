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
    /**
     * Toggle the visibility of the progress bar. Set to false to hide it.
     */
    protected $showProgressBar = true;

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
                ->render(function (Product $product) {
                    return $product->category->name ?: '-';
                }),


            TD::make('stockMovements', 'Stock')
                ->render(function (Product $product) {
                    $user = Auth::user();
                    $shop = $user->shop;

                    if (!$shop) {
                        return '<div class="text-muted">Pas de boutique liée</div>';
                    }

                    // Priorité au stock courant enregistré dans products (mis à jour par StockMovement hooks)
                    $currentStock = $product->stock_quantity;

                    if ((float) $currentStock <= 0) {
                        return '<div class="text-muted">Aucun stock</div>';
                    }

                    // Si la progression est désactivée, n'affiche que le label
                    if (! $this->showProgressBar) {
                        $stockLabelOnly = "{$currentStock} en stock";
                        return "<div><strong>{$stockLabelOnly}</strong></div>";
                    }

                    // Récupérer le dernier mouvement d'entrée pour ce produit / boutique
                    $lastEntry = $product->stockMovements()
                        ->where('shop_id', $shop->id)
                        ->where('type', 'entry')
                        ->orderByDesc('created_at')
                        ->first();

                    // Si pas d'entrée trouvée (rare si stock > 0), on tombe back au calcul global
                    if (! $lastEntry) {
                        $totalEntree = $product->stockMovements()
                            ->where('shop_id', $shop->id)
                            ->where('type', 'entry')
                            ->sum('quantity');

                        $totalSortie = $product->stockMovements()
                            ->where('shop_id', $shop->id)
                            ->where('type', 'exit')
                            ->sum('quantity');

                        $pourcentage = $totalEntree > 0 ? round(($totalSortie / $totalEntree) * 100) : 0;
                    } else {
                        // Calculer le stock AVANT le dernier réapprovisionnement
                        $entriesBefore = $product->stockMovements()
                            ->where('shop_id', $shop->id)
                            ->where('type', 'entry')
                            ->where('created_at', '<', $lastEntry->created_at)
                            ->sum('quantity');

                        $exitsBefore = $product->stockMovements()
                            ->where('shop_id', $shop->id)
                            ->where('type', 'exit')
                            ->where('created_at', '<', $lastEntry->created_at)
                            ->sum('quantity');

                        $stockBeforeLastEntry = (float) ($entriesBefore - $exitsBefore);

                        if ($stockBeforeLastEntry <= 0) {
                            // C'est un vrai réapprovisionnement après rupture : baseline = quantité entrée
                            $baseline = (float) $lastEntry->quantity;

                            $exitsSince = $product->stockMovements()
                                ->where('shop_id', $shop->id)
                                ->where('type', 'exit')
                                ->where('created_at', '>=', $lastEntry->created_at)
                                ->sum('quantity');

                            $pourcentage = $baseline > 0 ? round(($exitsSince / $baseline) * 100) : 0;
                        } else {
                            // Il restait du stock au moment du réapprovisionnement -> ne pas considérer
                            // uniquement la dernière entrée comme baseline (évite remise à 0 trompeuse).
                            // On retombe sur le calcul global (toutes entrées vs toutes sorties).
                            $totalEntree = $product->stockMovements()
                                ->where('shop_id', $shop->id)
                                ->where('type', 'entry')
                                ->sum('quantity');

                            $totalSortie = $product->stockMovements()
                                ->where('shop_id', $shop->id)
                                ->where('type', 'exit')
                                ->sum('quantity');

                            $pourcentage = $totalEntree > 0 ? round(($totalSortie / $totalEntree) * 100) : 0;
                        }
                    }

                    // Clamp percent
                    $pourcentage = max(0, min(100, $pourcentage));

                    // Couleur selon le pourcentage
                    $couleur = 'bg-success';
                    if ($pourcentage >= 95) {
                        $couleur = 'bg-danger';
                    } elseif ($pourcentage >= 80) {
                        $couleur = 'bg-warning';
                    }

                    // Affiche stock actuel + barre de progression
                    // Format the stock label to avoid trailing zeros for whole numbers
                    $formattedStock = number_format($currentStock, 2, '.', '');
                    $formattedStock = rtrim(rtrim($formattedStock, '0'), '.');
                    $stockLabel = $currentStock >= 0 ? "{$formattedStock} en stock" : "0 en stock";

                    return <<<HTML
                        <div>
                            <div class="small mb-1">
                                <strong>{$stockLabel}</strong>
                                <span class="float-right">{$pourcentage}% vendu</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar {$couleur}" role="progressbar" style="width: {$pourcentage}%;" aria-valuenow="{$pourcentage}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    HTML;
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
                                ->method('remove', [
                                    'id' => $product->id,
                                ]),
                        ]);
                }),
            
            
        ];
    }
}
