<?php

declare(strict_types=1);

namespace App\Orchid;

use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;
use Orchid\Support\Color;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

class PlatformProvider extends OrchidServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param Dashboard $dashboard
     *
     * @return void
     */
    public function boot(Dashboard $dashboard): void
        {
            parent::boot($dashboard);

            Route::get('/dashboard', function () {
                $user = Auth::user();

                if ($user && $user->inRole('vendeur')) {
                    return redirect()->route('platform.vendeur.dashboard');
                }

                return redirect()->route(config('platform.index'));
            });
        }

    /**
     * Register the application menu.
     *
     * @return Menu[]
     */
    public function menu(): array
        {
            $user = auth()->user();
            $menu = [];

            if ($user && !$user->inRole('vendeur')) {
                $menu[] = Menu::make('Acceuil')
                    ->icon('bs.house')
                    ->title('Navigation')
                    ->route(config('platform.index'));

                $menu[] = Menu::make(__('Ventes'))
                    ->icon('bs.house-door')
                    ->title('Ventes & Devis')
                    ->route('platform.ventes');

                $menu[] = Menu::make(__('Boutiques'))
                    ->icon('bs.shop-window')
                    ->title('Gestion des factures & Devis')
                    ->route('platform.shop');

                $menu[] = Menu::make(__('Fournisseurs'))
                    ->icon('bs.truck')
                    ->route('platform.Fournisseurs');

                
            }

            if ($user && $user->hasAccess('vendeur.dashboard')) {
                $menu[] = Menu::make('Acceuil')
                    ->icon('bs.house-door')
                    ->title('Dashbord')
                    ->route('platform.vendeur.dashboard');

                $menu[] = Menu::make(__('Commandes'))
                    ->icon('bs.cart')
                    ->title('Gestion des commandes & Devis')
                    ->route('platform.Commandes');
                    
                $menu[] = Menu::make(__('Fabrication'))
                    ->icon('bs.border')
                    ->route('platform.Fabrication');

                $menu[] = Menu::make(__('Produits'))
                    ->icon('bs.basket')
                    ->title('Produits')
                    ->route('platform.Product');
            }


            $menu[] = Menu::make(__('Users'))
                ->icon('bs.people')
                ->route('platform.systems.users')
                ->permission('platform.systems.users')
                ->title(__('Access Controls'));

            $menu[] = Menu::make(__('Roles'))
                ->icon('bs.shield')
                ->route('platform.systems.roles')
                ->permission('platform.systems.roles')
                ->divider();

            return $menu;
        }


    /**
     * Register permissions for the application.
     *
     * @return ItemPermission[]
     */
    public function permissions(): array
    {
        return [
            ItemPermission::group(__('System'))
                ->addPermission('platform.systems.roles', __('Roles'))
                ->addPermission('platform.systems.users', __('Users')),

            ItemPermission::group('Vendeur')
                ->addPermission('vendeur.dashboard', 'Dashboard Vendeur')
                ->addPermission('sales.view', 'Voir les ventes')
                ->addPermission('sales.create', 'Créer une vente'),

        ];
    }
}
