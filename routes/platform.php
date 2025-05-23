<?php

declare(strict_types=1);





use App\Orchid\Screens\PlatformScreen;
use App\Orchid\Screens\ProductScreen;
use App\Orchid\Screens\DocsScreen;
use App\Orchid\Screens\StockScreen;
use App\Orchid\Screens\CommandesScreen;
use App\Orchid\Screens\FacturePreviewScreen;

use App\Orchid\Screens\Crud\EditCommandeScreen;
use App\Orchid\Screens\FournisseursScreen;
use App\Orchid\Screens\crud\EditProductScreen;
use App\Orchid\Screens\crud\AddProductScreen;
use App\Orchid\Screens\crud\AddFournisseursScreen;
use App\Orchid\Screens\crud\EditFournisseursScreen;

use App\Orchid\Screens\Role\RoleEditScreen;
use App\Orchid\Screens\Role\RoleListScreen;
use App\Orchid\Screens\User\UserEditScreen;
use App\Orchid\Screens\User\UserListScreen;
use App\Orchid\Screens\User\UserProfileScreen;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the need "dashboard" middleware group. Now create something great!
|
*/

// Main
Route::screen('/main', PlatformScreen::class)
    ->name('platform.main');

// Platform > Product
Route::screen('Product', ProductScreen::class)
    ->name('platform.Product')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Product'), route('platform.Product')));

// Platform > Product > Add
Route::screen('Product/add', AddProductScreen::class)
    ->name('platform.Product.add')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.Product')
        ->push(__('Add Product'), route('platform.Product.add')));

// Platform > Product > Edit
Route::screen('Product/edit/{product}', EditProductScreen::class)
    ->name('platform.Product.edit')
    ->breadcrumbs(fn (Trail $trail, $id) => $trail
        ->parent('platform.Product')
        ->push(__('Edit Product'), route('platform.Product.edit', $id)));



Route::screen('Docs', DocsScreen::class)
    ->name('platform.Docs');

// Platform > Commandes
Route::screen('Commandes', CommandesScreen::class)
    ->name('platform.Commandes');

// Platform > Commandes > Edit
Route::screen('Commandes/edit/{vente}', EditCommandeScreen::class)
    ->name('platform.Commandes.edit');
    // ->breadcrumbs(fn (Trail $trail, $id) => $trail
    //     ->parent('platform.Commandes')
    //     ->push(__('Edit Vente'), route('platform.Commandes.edit', $id)));


// Platform > Fournisseurs
Route::screen('Fournisseurs', FournisseursScreen::class)
    ->name('platform.Fournisseurs')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Fournisseurs'), route('platform.Fournisseurs')));

// Platform > Fournisseurs > Add
Route::screen('Fournisseurs/addFournisseurs', AddFournisseursScreen::class)
    ->name('platform.Fournisseurs.addFournisseurs')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.Fournisseurs')
        ->push(__('Add Fournisseur'), route('platform.Fournisseurs.addFournisseurs')));

// Platform > Fournisseurs > Edit
Route::screen('Fournisseurs/editFournisseurs/{supplier}', editFournisseursScreen::class)
    ->name('platform.Fournisseurs.editFournisseurs')
    ->breadcrumbs(fn (Trail $trail, $id) => $trail
        ->parent('platform.Fournisseurs')
        ->push(__('Edit Fournisseur'), route('platform.Fournisseurs.editFournisseurs', $id)));


// Platform > Facture
Route::screen('facture/preview', FacturePreviewScreen::class)
    ->name('platform.facture.preview')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Facture'), route('platform.facture.preview')));


Route::get('facture/download', [FactureController::class, 'download'])
    ->name('platform.facture.download');


// Platform > Profile
Route::screen('profile', UserProfileScreen::class)
    ->name('platform.profile')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Profile'), route('platform.profile')));

// Platform > System > Users > User
Route::screen('users/{user}/edit', UserEditScreen::class)
    ->name('platform.systems.users.edit')
    ->breadcrumbs(fn (Trail $trail, $user) => $trail
        ->parent('platform.systems.users')
        ->push($user->name, route('platform.systems.users.edit', $user)));

// Platform > System > Users > Create
Route::screen('users/create', UserEditScreen::class)
    ->name('platform.systems.users.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.users')
        ->push(__('Create'), route('platform.systems.users.create')));

// Platform > System > Users
Route::screen('users', UserListScreen::class)
    ->name('platform.systems.users')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Users'), route('platform.systems.users')));

// Platform > System > Roles > Role
Route::screen('roles/{role}/edit', RoleEditScreen::class)
    ->name('platform.systems.roles.edit')
    ->breadcrumbs(fn (Trail $trail, $role) => $trail
        ->parent('platform.systems.roles')
        ->push($role->name, route('platform.systems.roles.edit', $role)));

// Platform > System > Roles > Create
Route::screen('roles/create', RoleEditScreen::class)
    ->name('platform.systems.roles.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.roles')
        ->push(__('Create'), route('platform.systems.roles.create')));

// Platform > System > Roles
Route::screen('roles', RoleListScreen::class)
    ->name('platform.systems.roles')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Roles'), route('platform.systems.roles')));


