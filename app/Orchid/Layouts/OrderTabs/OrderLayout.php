<?php

namespace App\Orchid\Layouts\OrderTabs;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use App\Models\Order;   
use Orchid\Screen\Actions\Link;

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
            
            TD::make('total_amount', 'Montant total')
                ->render(function (Order $order) {
                    return number_format($order->total_amount, 2, ',', ' ') . ' F CFA';
                }),

            TD::make('status', 'Statut')
                ->render(function (Order $order) {
                    return $order->status === 'pending' ? 'En attente' : 'Terminé';
                }),


            TD::make('pdf', 'PDF')
                ->render(function (Order $order) {
                    return Button::make('Télécharger')
                        ->method('downloadPDF')
                        ->parameters(['id' => $order->id])
                        ->icon('bs.file-earmark-pdf')
                        ->class('btn btn-success btn-sm');
                }),

            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn (Order $order) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([

                        Link::make(__('Edit'))
                            ->icon('bs.pencil'),

                        Button::make(__('Delete'))
                            ->icon('bs.trash3')
                            ->confirm(__('Once the account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.'))
                            
                    ])),

            


        ];
    }
}
