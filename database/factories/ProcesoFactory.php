<?php

namespace Database\Factories;

use App\Models\Proceso;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Proceso>
 */
class ProcesoFactory extends Factory
{
    protected $model = Proceso::class;

    private static array $monedas = ['COP', 'USD', 'EUR'];

    public function definition(): array
    {
        $fechaInicio = fake()->dateTimeBetween('-3 months', '-1 week');
        $fechaCierre  = fake()->dateTimeBetween($fechaInicio->format('Y-m-d') . ' +1 week', '+2 months');
        $moneda      = fake()->randomElement(self::$monedas);

        $presupuesto = match ($moneda) {
            'COP' => fake()->numberBetween(50_000_000, 500_000_000),
            'USD' => fake()->numberBetween(10_000, 200_000),
            'EUR' => fake()->numberBetween(8_000, 150_000),
        };

        return [
            'objeto'       => fake()->sentence(6, true),
            'actividad'    => fake()->sentence(4, true),
            'descripcion'  => fake()->paragraph(3, true),
            'fecha_inicio' => $fechaInicio->format('Y-m-d'),
            'hora_inicio'  => fake()->randomElement(['08:00', '09:00', '10:00', '14:00']),
            'fecha_cierre' => $fechaCierre->format('Y-m-d'),
            'hora_cierre'  => fake()->randomElement(['16:00', '17:00', '18:00']),
            'presupuesto'   => $presupuesto,
            'moneda'       => $moneda,
        ];
    }
}
