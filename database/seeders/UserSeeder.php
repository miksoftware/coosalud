<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@coosalud.local'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('admin123'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'consulta@coosalud.local'],
            [
                'name' => 'Usuario Consulta',
                'password' => bcrypt('consulta123'),
                'role' => 'consulta',
            ]
        );
    }
}
