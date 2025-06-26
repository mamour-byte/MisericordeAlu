<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Password;
use Orchid\Screen\Layouts\Rows;

class ProfilePasswordLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            Password::make('old_password')
                ->placeholder(__('Entrer l\'actuel mot de passe'))
                ->title(__('Actuel mot de passe')),
                // ->help('This is your password set at the moment.'),

            Password::make('password')
                ->placeholder(__('Entrer le nouveau mot de passe'))
                ->title(__('Nouveau mot de passe')),

            Password::make('password_confirmation')
                ->placeholder(__('Enter le mot de passe à confirmer'))
                ->title(__('Confirmé le mot de passe'))
                ->help('Un bon mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre.'),
        ];
    }
}
