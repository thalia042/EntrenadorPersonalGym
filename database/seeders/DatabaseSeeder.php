<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Crear Usuario Administrador / Entrenador
        User::create([
            'name' => 'Entrenador Principal',
            'email' => 'admin@gym.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Crear un Cliente de prueba
        User::create([
            'name' => 'Juan Pérez (Cliente)',
            'email' => 'cliente@gym.com',
            'password' => Hash::make('password123'),
            'role' => 'client',
        ]);
    }
}
