<?php

namespace Database\Seeders;

use App\Models\Responsable;
use Illuminate\Database\Seeder;

class ResponsableSeeder extends Seeder
{
    public function run(): void
    {
        $responsables = [
            ['nombre_completo' => 'Carlos Gómez',     'numero_telefono' => '3001234567', 'correo_electronico' => 'carlos.gomez@example.com'],
            ['nombre_completo' => 'María Rodríguez',  'numero_telefono' => '3109876543', 'correo_electronico' => 'maria.rodriguez@example.com'],
            ['nombre_completo' => 'Juan Pérez',       'numero_telefono' => '3205551234', 'correo_electronico' => 'juan.perez@example.com'],
            ['nombre_completo' => 'Ana Martínez',     'numero_telefono' => '3004445678', 'correo_electronico' => 'ana.martinez@example.com'],
            ['nombre_completo' => 'Pedro López',      'numero_telefono' => '3157778910', 'correo_electronico' => 'pedro.lopez@example.com'],
        ];

        foreach ($responsables as $data) {
            Responsable::firstOrCreate(
                ['correo_electronico' => $data['correo_electronico']],
                $data
            );
        }

        $this->command->info('Responsables creados: ' . count($responsables));
    }
}
