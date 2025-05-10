<?php

namespace App\Orchid\Layouts;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Button;
use App\Models\Supplier;

class FournisseurLayout extends Table
{
    protected $target = 'suppliers';

    protected function columns(): iterable
    {
        return [
            TD::make('name', 'Nom')
                ->render(fn(Supplier $supplier) => $supplier->name),

            TD::make('email', 'Email')
                ->render(fn(Supplier $supplier) => $supplier->email),

            TD::make('phone', 'Contact')
                ->render(fn(Supplier $supplier) => $supplier->phone),

            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn(Supplier $supplier) =>
                    DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
                            Link::make('Modifier')
                                ->icon('bs.pencil')
                                ->route('platform.Fournisseurs.editFournisseurs', $supplier->id),

                            Button::make('Supprimer')
                                ->icon('bs.trash3')
                                ->confirm('Cette action est irréversible.')
                                ->method('delete', [
                                    'id' => $supplier->id,
                                ]),
                        ])
                ),
        ];
    }
}
