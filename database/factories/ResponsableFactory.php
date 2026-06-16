<?php

namespace Database\Factories;

use App\Models\Responsable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Responsable>
 */
class ResponsableFactory extends Factory
{
    protected $model = Responsable::class;

    public function definition(): array
    {
        return [
            'nombre_completo'    => fake()->name(),
            'numero_telefono'    => fake()->phoneNumber(),
            'correo_electronico' => fake()->unique()->safeEmail(),
        ];
    }
}
