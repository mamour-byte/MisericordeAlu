<?php

namespace App\Orchid\Layouts\OrderTabs;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use App\Models\Order;   
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Label;

class OrderLayout extends Table
{
    protected $target = 'Commandes';

    protected function columns(): iterable
    {
        return [
            TD::make('customer_name', 'Client')
                ->render(function (Order $order) {
                    return $order->customer_name ? $order->customer_name : 'Client inconnu';
                }),

            TD::make('produits', 'Produits')
                ->render(function (Order $order) {
                    return $order->items->map(function ($item) {
                        return $item->product->name ?? 'N/A';
                    })->implode(', ');
                }),

            TD::make('status', 'Statut')
                ->sort()
                ->render(function (Order $order) {
                    if ($order->status === 'pending') {
                        return '<span style="color: red; font-weight: bold;">En attente</span>';
                    } elseif ($order->status === 'approved') {
                        return '<span style="color: green; font-weight: bold;">Validé</span>';
                    }
                    return $order->status;
                }),

            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn (Order $order) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([

                        Link::make(__('Edit'))
                            ->icon('bs.pencil')
                            ->route('platform.Commandes.edit', $order->id),

                        Button::make(__('Delete'))
                            ->icon('bs.trash3')
                            ->confirm(__('Once the account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.'))
                            
                    ])),


            TD::make('pdf', 'PDF')
                ->render(function (Order $order) {
                    return Link::make('')
                        ->method('downloadPDF')
                        ->icon('bs.file-earmark-pdf')
                        ->class('btn btn-success btn-sm')
                        ->route('platform.facture.preview', [
                            'id' => $order->id,
                        ]);
                }),

            


        ];
    }
}
