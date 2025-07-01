<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class MainRedirectScreen extends Screen
{
    public function handle(Request $request, ...$arguments)
    {
        $user = Auth::user();
        if ($user && $user->inRole('Vendeur')) {
            return redirect()->route('platform.vendeur.dashboard');
        }
        return redirect()->route('platform.main.admin');
    }

    public function query(): array
    {
        return [];
    }

    public function layout(): array
    {
        return [];
    }
}