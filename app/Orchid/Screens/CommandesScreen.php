<?php

namespace App\Orchid\Screens;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

use Illuminate\Http\Request;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Alert;
use App\Http\Controllers\OrderController;
use App\Orchid\Layouts\OrderTabs\OrderLayout;
use App\Orchid\Layouts\OrderTabs\NewOrderLayout;
use App\Orchid\Filters\OrderFilterLayout;
use Orchid\Support\Facades\Toast;


class CommandesScreen extends Screen
{
    
    public $exists = true;

    public $name = 'Commandes';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): iterable
        {
            $user = auth()->user();

            // Supposons que la relation s'appelle 'shop'
            if (!$user->shop) {
                Toast::error("Aucun magasin ne vous a été attribué. Veuillez contacter l'administrateur.");
                return [
                    'Commandes' => collect(), 
                ];
            }

            return [
                'Commandes' => Order::with(['items.product'])
                    ->where('user_id', $user->id)
                    ->where('archived', 'non')
                    ->latest()
                    ->paginate(10),
            ];
        }

    /**
     * Button commands.
     *
     * @return iterable
     */
    public function name(): ?string
    {
        return 'Créer une commande';
    }


    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * Views.
     *
     * @return iterable
     */
    public function layout(): iterable
    {
        return [
            Layout::tabs([
                'Nouvelle Vente' => [NewOrderLayout::class,],
                'Historique' => [OrderLayout::class,],
                ],)
        ];
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(Request $request)
    {

        return app(OrderController::class)->save($request);
    }
}
