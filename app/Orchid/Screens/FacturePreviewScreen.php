<?php

namespace App\Orchid\Screens;

use Illuminate\Http\Request;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class FacturePreviewScreen extends Screen
{
     public $name = 'Aperçu du document PDF';

        public function query(Request $request): iterable
        {
            return [
                'OrderId' => $request->id,
            ];
        }

        public function layout(): array
        {
            return [
                Layout::view('orchid.preview-pdf'),
            ];
        }
}
