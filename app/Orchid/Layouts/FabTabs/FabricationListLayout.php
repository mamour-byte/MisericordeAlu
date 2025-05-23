<?php

namespace App\Orchid\Layouts\FabTabs;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use App\Models\Fabrication;
use App\Models\FabricationItem;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;


class FabricationListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'fabrications';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
             
            TD::make('customer_name', 'Nom du client')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (Fabrication $fabrication) {
                    return $fabrication->customer_name;
                }),

            TD::make('type', 'Type de produit')
                ->sort()
                ->render(function (Fabrication $fabrication) {
                    return $fabrication->items->map(function (FabricationItem $item) {
                        $type = $item->type ?? 'N/A';
                        $quantity = $item->quantity ?? 0; // Assurez-vous que le champ `quantity` existe dans votre modèle
                        return "{$type} ({$quantity})";
                    })->implode(', ');
                }),
                
            TD::make('status', 'Statut')
                ->sort()
                ->render(function (Fabrication $fabrication) {
                    if ($fabrication->status === 'quote') {
                        return '<span style="color: red; font-weight: bold;">En attente</span>';
                    } elseif ($fabrication->status === 'invoice') {
                        return '<span style="color: green; font-weight: bold;">Validé</span>';
                    }
                    return $fabrication->status;
                }),

            TD::make('created_at', 'Date de création')
                ->sort()
                ->render(function (Fabrication $fabrication) {
                    return $fabrication->created_at->format('d/m/Y');
                }),

            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn (Fabrication $fabrication) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([

                        Link::make(__('Edit'))
                            ->icon('bs.pencil'),

                        Button::make(__('Delete'))
                            ->icon('bs.trash3')
                            ->confirm(__('Once the account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.'))
                            
                    ])),


            TD::make('pdf', 'PDF')
                ->render(function (Fabrication $fabrication) {
                    return Link::make('')
                        ->method('downloadPDF')
                        ->icon('bs.file-earmark-pdf')
                        ->class('btn btn-success btn-sm')
                        ->route('platform.facture.preview', [
                            // 'id' => $fabrication->id,
                        ]);
                }),
        ];
    }
}
