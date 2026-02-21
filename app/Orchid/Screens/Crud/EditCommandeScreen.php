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
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Services\NumberGenerator;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


/**
 * Screen for editing an existing order.
 * 
 * This screen allows users to modify order details, including customer information,
 * products, quantities, and document type. When an order is modified, the original
 * order is archived and a credit note (avoir) is created for traceability.
 */
class EditCommandeScreen extends Screen
{
    /**
     * The order being edited.
     */
    public ?Order $order = null;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @param Order $order The order to edit
     * @return iterable
     */
    public function query(Order $order): iterable
    {
        $order->load(['items.product']);
        $this->order = $order;

        // Préparer les valeurs par défaut pour les produits et quantités
        $defaultProducts = $order->items->pluck('product_id')->toArray();
        $defaultQuantities = $order->items->pluck('quantity')->map(function($q) {
            return rtrim(rtrim(number_format($q, 2, '.', ''), '0'), '.');
        })->toArray();
        $defaultQuantitiesString = implode(',', $defaultQuantities);

        return [
            'order' => $order,
            'defaultProducts' => $defaultProducts,
            'defaultQuantities' => $defaultQuantitiesString,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->order 
            ? 'Modifier la commande #' . $this->order->id 
            : 'Modifier la commande';
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
                            ->required()
                            ->value(fn($data) => $data['defaultProducts'] ?? []),

                        Input::make('order.quantities') 
                            ->title('Quantités')
                            ->type('text')
                            ->required()
                            ->help('Format: 1,2,3 ou 1,0.5,2.5 — les quantités décimales sont acceptées')
                            ->value(fn($data) => $data['defaultQuantities'] ?? ''),

                    ]),

                    Group::make([

                    Input::make('order.remise') 
                        ->title('Remise')
                        ->type('number')
                        ->help('Ajouter des remises aux factures (ex: 5000)')
                        ->step('0.01'),

                    Select::make('order.Docs')
                        ->title('Type de document')
                        ->options([
                            'Quote'   => 'Devis',
                            'Invoice' => 'Facture',
                        ])
                        ->empty('Sélectionnez un type'),



                ]),



                ])
            ];
        }


    /**
     * Handle the order update.
     *
     * This method:
     * 1. Archives the original order and marks it as canceled
     * 2. Creates stock entry movements to reverse previous exits
     * 3. Creates a credit note (avoir) for the original order
     * 4. Creates a new order with updated information
     * 5. Validates stock availability for invoices
     * 6. Creates stock movements and invoice/quote documents
     *
     * @param Request $request The form request containing order data
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
        {
            $data = $request->get('order');
            $originalOrder = Order::findOrFail($data['id']);

            DB::beginTransaction();

            try {
                // 1. Archiver l'ancienne commande
                $originalOrder->update(['archived' => 'oui' , 'status' => 'canceled']);

            // 1.b Annuler les anciens mouvements de stock en créant des mouvements d'entrée
            // (au lieu de supprimer, on crée des entrées pour annuler les sorties)
            $oldMovements = StockMovement::where('order_id', $originalOrder->id)
                ->where('type', StockMovement::TYPE_EXIT)
                ->get();
            
            foreach ($oldMovements as $oldMovement) {
                // Créer un mouvement d'entrée pour annuler la sortie précédente
                StockMovement::create([
                    'product_id' => $oldMovement->product_id,
                    'order_id'   => $originalOrder->id,
                    'shop_id'    => $oldMovement->shop_id,
                    'type'       => StockMovement::TYPE_ENTRY,
                    'quantity'   => $oldMovement->quantity,
                    'notes'      => 'Annulation de la commande #' . $originalOrder->id,
                ]);
                // Remettre la quantité dans le stock réel
                $product = Product::find($oldMovement->product_id);
                if ($product) {
                    $product->stock_quantity += $oldMovement->quantity;
                    $product->save();
                }
            }

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

            // Validation du stock disponible avant de créer la nouvelle commande
            foreach ($products as $index => $productId) {
                $product = Product::findOrFail($productId);
                $quantity = (float)$quantities[$index];
                
                // Vérifier que le produit appartient à la boutique
                if ($product->shop_id !== $newOrder->shop_id) {
                    DB::rollBack();
                    Toast::error("Le produit '{$product->name}' n'appartient pas à votre boutique.");
                    return redirect()->back();
                }
                
                // Vérifier le stock disponible (uniquement pour les factures)
                if ($documentType === 'Invoice' && $product->stock_quantity < $quantity) {
                    DB::rollBack();
                    $stockDisponible = number_format($product->stock_quantity, 2, ',', ' ');
                    Toast::error("Stock insuffisant pour '{$product->name}'. Stock disponible : {$stockDisponible}");
                    return redirect()->back();
                }
            }

            foreach ($products as $index => $productId) {
                $product = Product::findOrFail($productId);
                $quantity = (float)$quantities[$index];
                $unit_price = (float)$product->price;
                $item_total = $quantity * $unit_price;

                $newOrder->items()->create([
                    'product_id' => $productId,
                    'quantity'   => $quantity,
                    'unit_price' => $unit_price,
                    'no_order'   => $noOrder,
                ]);

                // 3.b Créer le mouvement de stock pour chaque produit (uniquement pour les factures)
                if ($documentType === 'Invoice') {
                    StockMovement::create([
                        'product_id' => $productId,
                        'order_id'   => $newOrder->id,
                        'shop_id'    => $newOrder->shop_id,
                        'type'       => StockMovement::TYPE_EXIT,
                        'quantity'   => $quantity,
                        'notes'      => 'Vente modifiée, commande #' . $newOrder->id,
                    ]);
                    // Retirer la quantité du stock réel
                    $product->stock_quantity -= $quantity;
                    $product->save();
                }

                $total += $item_total;
            }

                $newOrder->update(['total_amount' => $total]);

                // 4. Générer la facture ou devis
                $remise = isset($data['remise']) ? (float) $data['remise'] : 0;
                $documentNumber = NumberGenerator::generateDocumentNumber($documentType);

                if ($documentType === 'Invoice') {
                    $invoice = Invoice::create([
                        'customer_name'    => $data['customer_name'],
                        'customer_email'   => $data['customer_email'],
                        'customer_phone'   => $data['customer_phone'],
                        'customer_address' => $data['customer_address'],
                        'status'           => 'approved',
                        'total_amount'     => max(0, $total - $remise),
                        'user_id'          => Auth::id(),
                        'shop_id'          => $newOrder->shop_id,
                    ]);

                    $newOrder->invoice_id = $invoice->id;
                    $newOrder->save();

                    foreach ($newOrder->items as $item) {
                        InvoiceItem::create([
                            'invoice_id' => $invoice->id,
                            'no_invoice' => $documentNumber,
                            'product_id' => $item->product_id,
                            'quantity'   => (float) $item->quantity,
                            'unit_price' => number_format((float) $item->unit_price, 2, '.', ''),
                        ]);
                    }
                } else {
                    $quote = Quote::create([
                        'customer_name'    => $data['customer_name'],
                        'customer_email'   => $data['customer_email'],
                        'customer_phone'   => $data['customer_phone'],
                        'customer_address' => $data['customer_address'],
                        'status'           => 'pending',
                        'total_amount'     => max(0, $total - $remise),
                        'user_id'          => Auth::id(),
                        'shop_id'          => $newOrder->shop_id,
                    ]);

                    $newOrder->quote_id = $quote->id;
                    $newOrder->save();

                    foreach ($newOrder->items as $item) {
                        QuoteItem::create([
                            'quote_id' => $quote->id,
                            'no_quote' => $documentNumber,
                            'product_id' => $item->product_id,
                            'quantity'   => (float) $item->quantity,
                            'unit_price' => number_format((float) $item->unit_price, 2, '.', ''),
                        ]);
                    }
                }
            DB::commit();
            Toast::success('Commande modifiée avec succès.');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            $message = app()->environment('local')
                ? "Erreur : " . $e->getMessage()
                : "Une erreur est survenue lors de la modification de la commande.";
            Toast::error($message);
        }
        return redirect()->route('platform.Commandes');
    }
}