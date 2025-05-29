<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Illuminate\Http\Request;

class DevisPreviewScreen extends Screen
{
    public $name = 'Aperçu du document PDF';

        public function query(Request $request): iterable
        {
            return [
                // 'FabricationId' => $request->id,
            ];
        }

        public function layout(): array
        {
            return [
                Layout::view('orchid.preview-quote-pdf'),
            ];
        }
}
