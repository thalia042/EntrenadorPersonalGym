<?php

namespace Database\Seeders;

use App\Models\Exercise;
use App\Models\HabitLog;
use App\Models\Payment;
use App\Models\Routine;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear Usuario Administrador / Entrenador
        $admin = User::create([
            'name' => 'Entrenador Admin',
            'email' => 'admin@gym.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // 2. Crear Clientes de Prueba
        $cliente1 = User::create([
            'name' => 'Juan Pérez',
            'email' => 'juan@gym.com',
            'password' => Hash::make('password123'),
            'role' => 'client',
        ]);

        $cliente2 = User::create([
            'name' => 'María Gómez',
            'email' => 'maria@gym.com',
            'password' => Hash::make('password123'),
            'role' => 'client',
        ]);

        // 3. Crear Ejercicios
        $pressBanca = Exercise::create([
            'name' => 'Press de Banca Plano',
            'muscle_group' => 'Pecho',
            'description' => 'Empuje horizontal en banco plano con barra.',
        ]);

        $sentadilla = Exercise::create([
            'name' => 'Sentadilla Trasera',
            'muscle_group' => 'Piernas',
            'description' => 'Flexión de rodillas y cadera con barra sobre los trapecios.',
        ]);

        // 4. Crear Rutina y Vincular Ejercicios
        $rutinaJuan = Routine::create([
            'user_id' => $cliente1->id,
            'name' => 'Hipertrofia Torso-Pierna',
            'description' => 'Enfoque en ganancia de masa muscular de alta intensidad.',
        ]);

        $rutinaJuan->exercises()->attach([
            $pressBanca->id => ['sets_reps' => '4 series x 10 repeticiones'],
            $sentadilla->id => ['sets_reps' => '4 series x 8 repeticiones'],
        ]);

        // 5. Crear Pagos
        Payment::create([
            'user_id' => $cliente1->id,
            'amount' => 15000,
            'payment_date' => now()->subDays(5),
            'due_date' => now()->addDays(25),
            'status' => 'paid',
        ]);

        Payment::create([
            'user_id' => $cliente2->id,
            'amount' => 15000,
            'payment_date' => now(),
            'due_date' => now()->addDays(2),
            'status' => 'pending',
        ]);

        // 6. Registro de Hábitos
        HabitLog::create([
            'user_id' => $cliente1->id,
            'water_liters' => 2.5,
            'sleep_hours' => 8,
            'log_date' => now()->toDateString(),
        ]);
    }
}
