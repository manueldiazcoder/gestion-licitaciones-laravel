<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Usuario administrador por defecto
        User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name'     => 'admin',
                'password' => Hash::make('admin'),
                'role'     => 'admin',
            ]
        );

        // Usuario visor de prueba
        User::firstOrCreate(
            ['email' => 'visor@visor.com'],
            [
                'name'     => 'visor',
                'password' => Hash::make('visor'),
                'role'     => 'visor',
            ]
        );

        $this->command->info('Usuarios por defecto creados:');
        $this->command->info('  admin / admin@admin.com / admin');
        $this->command->info('  visor / visor@visor.com / visor');

        // Seeders de datos
        $this->call(ResponsableSeeder::class);
        $this->call(ProcesoSeeder::class);

        $this->command->info('¡Todos los datos semilla fueron creados!');
    }
}
