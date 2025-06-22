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
use App\Models\Avoir;
use App\Models\AvoirItem;
use App\Services\NumberGenerator;


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
                            ->applyScope('byShop', auth()->user()->shop?->id)
                            ->searchColumns('name')
                            ->displayAppend('name')
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


    /**
     * Handle the form submission.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
        {
            $data = $request->get('order');
            $originalOrder = Order::findOrFail($data['id']);

            // 1. Archiver l’ancienne commande
            $originalOrder->update(['archived' => 'oui' , 'status' => 'canceled']);

            // 2. Générer l’avoir à partir de l’ancienne commande
            $noAvoir = NumberGenerator::generateCreditNoteNumber();

            $credit = Avoir::create([
                'no_avoir'         => $noAvoir,
                'order_id'         => $originalOrder->id,
                'user_id'          => $originalOrder->user_id,
                'shop_id'          => $originalOrder->shop_id,
                'customer_name'    => $originalOrder->customer_name,
                'customer_email'   => $originalOrder->customer_email,
                'customer_phone'   => $originalOrder->customer_phone,
                'customer_address' => $originalOrder->customer_address,
                'total_amount'     => $originalOrder->total_amount,
            ]);

            foreach ($originalOrder->items as $item) {
                $credit->items()->create([
                    'product_id' => $item->product_id,
                    'quantity'   => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'no_avoir'   => $noAvoir,
                ]);
            }

            // 3. Créer la nouvelle commande
            $noOrder = NumberGenerator::generateOrderNumber();
            $quantities = array_map('trim', explode(',', $data['quantities']));
            $products = $data['products'];
            $documentType = $data['Docs'] ?? 'Invoice';
            $total = 0;

            $newOrder = Order::create([
                'no_order'         => $noOrder,
                'customer_name'    => $data['customer_name'],
                'customer_email'   => $data['customer_email'],
                'customer_phone'   => $data['customer_phone'],
                'customer_address' => $data['customer_address'],
                'status'           => $documentType === 'Invoice' ? 'approved' : 'pending',
                'total_amount'     => 0,
                'user_id'          => $originalOrder->user_id,
                'shop_id'          => $originalOrder->shop_id,
            ]);

            foreach ($products as $index => $productId) {
                $product = Product::findOrFail($productId);
                $quantity = (int)$quantities[$index];
                $unit_price = (float)$product->price;
                $item_total = $quantity * $unit_price;

                $newOrder->items()->create([
                    'product_id' => $productId,
                    'quantity'   => $quantity,
                    'unit_price' => $unit_price,
                    'no_order'   => $noOrder,
                ]);

                $total += $item_total;
            }

            $newOrder->update(['total_amount' => $total]);

            // 4. Générer la facture ou devis comme dans ton code actuel (facultatif ici)

            Toast::info('Nouvelle commande créée à partir de l\'avoir.');
            return redirect()->route('platform.Commandes');
        }



}
