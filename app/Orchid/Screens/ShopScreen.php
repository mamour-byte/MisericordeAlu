<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use App\Models\Shop;
use Orchid\Screen\Layout;
use Orchid\Screen\Actions\Link;
use App\Orchid\Layouts\ShopListLayout;

class ShopScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    

    public function query(): iterable
    {
        return [
            'shops' => Shop::with('manager')->latest()->paginate(),
        ];
    }


    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Boutiques';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Créer une boutique')
                ->icon('plus')
                ->route('platform.shop.create'),
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
                ShopListLayout::class , 
            ];
        }
}
