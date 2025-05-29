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
            $Fabrication = $request->get('Fabrication');
            $items = $request->get('items');

            // Vérification des champs obligatoires
            if (!isset($Fabrication['customer_name'])) {
                Toast::error('Veuillez remplir tous les champs obligatoires.');
                return back();
            }

            // 1. Enregistrement de la fabrication
            $fabrication = Fabrication::create([
                'user_id'          => Auth::id(),  // utilisateur connecté
                'customer_name'    => $Fabrication['customer_name'],
                'customer_phone'   => $Fabrication['customer_phone'] ?? null,
                'customer_email'   => $Fabrication['customer_email'] ?? null,
                'customer_address' => $Fabrication['customer_address'] ?? null,
                'status'           => $Fabrication['docs'],  // correspond à 'quote' ou 'invoice'
            ]);

            // 2. Enregistrement des items liés
            foreach ($items as $item) {
                FabricationItem::create([
                    'fabrication_id' => $fabrication->id,
                    'type'           => $item['type'] ?? '',
                    'width'        => $item['width'] ?? 0,
                    'height'        => $item['height'] ?? 0,
                    'price_meter'     => $item['price_meter'] ?? 0,
                    'quantity'       => $item['quantity'] ?? 1,
                    'note'           => $item['note'] ?? null,
                ]);
            }

            Toast::success('La commande a été enregistrée avec succès !');
            return redirect()->route('platform.Fabrication'); // adapte cette route
        }
    
}
