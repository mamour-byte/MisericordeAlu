<?php

declare(strict_types=1);

namespace App\Orchid;

use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;
use Orchid\Support\Color;

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

        // ...
    }

    /**
     * Register the application menu.
     *
     * @return Menu[]
     */
    public function menu(): array
    {
        return [
            Menu::make('Acceuil')
                ->icon('bs.house')
                ->title('Navigation')
                ->route(config('platform.index')),

            Menu::make(__('Docs'))
                ->icon('bs.file-earmark-text')
                ->title('Gestion des factures & Devis')
                ->route('platform.Docs'),

            Menu::make(__('Commandes'))
                ->icon('bs.cart')
                ->route('platform.Commandes'),

            Menu::make(__('Fabrication'))
                ->icon('bs.border')
                ->route('platform.Fabrication'),
            
            Menu::make(__('Produits'))
                ->icon('bs.basket')
                ->title('Produits & Fournisseurs')
                ->route('platform.Product'),

            Menu::make(__('Fournisseurs'))
                ->icon('bs.truck')
                ->route('platform.Fournisseurs'),

            Menu::make(__('Users'))
                ->icon('bs.people')
                ->route('platform.systems.users')
                ->permission('platform.systems.users')
                ->title(__('Access Controls')),

            Menu::make(__('Roles'))
                ->icon('bs.shield')
                ->route('platform.systems.roles')
                ->permission('platform.systems.roles')
                ->divider(),

        ];
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
        ];
    }
}
