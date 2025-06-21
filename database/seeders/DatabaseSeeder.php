<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Orchid\Platform\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Appelle d'abord le seeder de rôles
        $this->call(RoleSeeder::class);

        // Crée un utilisateur de test
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Donne-lui un rôle (par exemple admin)
        $user->addRole('admin');
        // Ou si tu préfères vendeur :
        // $user->addRole('vendeur');
    }
}
