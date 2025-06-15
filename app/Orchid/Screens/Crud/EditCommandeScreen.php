<?php

namespace App\Orchid\Screens\Crud;

use Illuminate\Http\Request;
use Orchid\Support\Facades\Toast;

use Orchid\Screen\Screen;
use Orchid\Screen\Action;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Select;
use Orchid\Support\Facades\Layout;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;

class EditCommandeScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public $order;

    
    public function query(Order $order): iterable
        {
            $order->load(['items.product']);
            $this->order = $order;
            

            return [
                'order' => $order
            ];
        }




    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'EditCommandeScreen';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Enregistrer')
                ->method('update')
                ->icon('check'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
        {
            return [
                Layout::rows([
                    Input::make('order.id')->type('hidden'),

                    Group::make([
                        Input::make('order.customer_name')
                            ->title('Nom du client')
                            ->required(),

                        Input::make('order.customer_email')
                            ->title('Email')
                    ]),

                    Group::make([
                        Input::make('order.customer_phone')
                            ->title('Téléphone'),

                        Input::make('order.customer_address')
                            ->title('Adresse'),
                    ]),

                    Group::make([
                        Relation::make('order.products')
                            ->title('Produits')
                            ->fromModel(Product::class, 'name')
                            ->multiple()
                            ->required(),

                        Input::make('order.quantities') 
                            ->title('Quantités')
                            ->type('text')
                            ->required()
                            ->help('Format: 1,2,3'),

                    ]),

                    Select::make('order.Docs')
                        ->title('Type de document')
                        ->options([
                            'Quote'   => 'Devis',
                            'Invoice' => 'Facture',
                        ])
                        ->empty('Sélectionnez un type')
                ])


            ];
        }


    public function update(Request $request)
        {
            $data = $request->get('order');
            $order = Order::findOrFail($data['id']);

            $order->update([
                'customer_name'    => $data['customer_name'],
                'customer_email'   => $data['customer_email'],
                'customer_phone'   => $data['customer_phone'],
                'customer_address' => $data['customer_address'],
                'status'           => $data['Docs'],
            ]);

            $quantities = explode(',', $data['quantities']);
            $products = $data['products'];
            $orderItems = [];
            foreach ($products as $index => $productId) {
                $orderItems[] = [
                    'order_id'   => $order->id,
                    'product_id' => $productId,
                    'quantity'   => isset($quantities[$index]) ? (int)$quantities[$index] : 1,
                ];
            }

            Toast::info('Commande mise à jour avec succès.');
            return redirect()->route('platform.Commandes');
        }


}
