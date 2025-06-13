<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use App\Models\Fabrication;
use App\Models\FabricationItem;
use Orchid\Support\Facades\Toast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Orchid\Layouts\FabTabs\NewFabricationLayout;
use App\Orchid\Layouts\FabTabs\FabricationListLayout;



class FabricationScreen extends Screen
{
    public function query(): iterable
    {
        return [
            'fabrications' => Fabrication::with('items')->latest()->paginate(10),
        ];
    }

    public function name(): ?string
    {
        return 'Devis Portes & Fenêtres';
    }

    public function commandBar(): iterable
    {
        return [];
    }

    public function layout(): iterable
        {
            return [
            Layout::tabs([
                'Nouvelle Fab' => [
                    new NewFabricationLayout(),
                ],
                'Historique Fab ' => [
                    new FabricationListLayout(),
                ],
            ]),
        ];
            
    }


    public function save(Request $request)
        {
            $order = $request->get('order'); // <-- important : correspond au préfixe dans le formulaire
            $items = $request->get('items');

            // Validation simple
            if (empty($order['customer_name']) || empty($order['docs'])) {
                Toast::error('Veuillez remplir tous les champs obligatoires.');
                return back();
            }

            if (empty($items) || !is_array($items)) {
                Toast::error('Veuillez ajouter au moins un produit.');
                return back();
            }

            // 1. Enregistrement de la fabrication
            $fabrication = Fabrication::create([
                'user_id'          => Auth::id(),
                'customer_name'    => $order['customer_name'],
                'customer_phone'   => $order['customer_phone'] ?? null,
                'customer_email'   => $order['customer_email'] ?? null,
                'customer_address' => $order['customer_address'] ?? null,
                'status'           => $order['docs'],  // quote ou invoice
            ]);

            // 2. Enregistrement des items liés
            foreach ($items as $item) {
                FabricationItem::create([
                    'fabrication_id' => $fabrication->id,
                    'type'           => $item['type'] ?? '',
                    'width'          => $item['width'] ?? 0,
                    'height'         => $item['height'] ?? 0,
                    'price_meter'    => $item['price_meter'] ?? 0,
                    'quantity'       => $item['quantity'] ?? 1,
                    'note'           => $item['note'] ?? null,
                ]);
            }

            Toast::success('La commande a été enregistrée avec succès !');
            return redirect()->route('platform.Fabrication');
        }

    
}
