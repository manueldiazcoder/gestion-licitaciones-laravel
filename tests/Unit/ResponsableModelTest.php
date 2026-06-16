<?php

namespace Tests\Unit;

use App\Models\Licitacion;
use App\Models\Responsable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResponsableModelTest extends TestCase
{
    use RefreshDatabase;

    // ─────────────────────────────────────────
    //  Factory
    // ─────────────────────────────────────────

    public function test_factory_crea_responsable_valido(): void
    {
        $responsable = Responsable::factory()->create();

        $this->assertNotNull($responsable->id);
        $this->assertNotNull($responsable->nombre_completo);
        $this->assertNotNull($responsable->numero_telefono);
        $this->assertNotNull($responsable->correo_electronico);
    }

    public function test_factory_crea_correo_unico(): void
    {
        Responsable::factory()->create(['correo_electronico' => 'repetido@test.com']);
        Responsable::factory()->create(['correo_electronico' => 'repetido@test.com']);

        $this->assertDatabaseCount('responsables', 2);
    }

    // ─────────────────────────────────────────
    //  Fillable
    // ─────────────────────────────────────────

    public function test_mass_assignment_crea_responsable(): void
    {
        $data = [
            'nombre_completo'    => 'Carlos Pérez',
            'numero_telefono'    => '3001234567',
            'correo_electronico' => 'carlos@example.com',
        ];

        Responsable::create($data);

        $this->assertDatabaseHas('responsables', $data);
    }

    public function test_mass_assignment_ignora_campos_no_fillable(): void
    {
        Responsable::create([
            'nombre_completo'    => 'Test',
            'numero_telefono'    => '123',
            'correo_electronico' => 'test@test.com',
            'role'               => 'admin', // no está en $fillable
        ]);

        $this->assertDatabaseHas('responsables', [
            'nombre_completo' => 'Test',
        ]);
    }

    // ─────────────────────────────────────────
    //  Relaciones
    // ─────────────────────────────────────────

    public function test_relacion_licitaciones_retorna_coleccion_vacia_sin_licitaciones(): void
    {
        $responsable = Responsable::factory()->create();

        $this->assertCount(0, $responsable->licitaciones);
    }

    public function test_relacion_licitaciones_retorna_licitaciones_asociadas(): void
    {
        $responsable = Responsable::factory()->create();
        Licitacion::factory()->count(2)->create(['responsable_id' => $responsable->id]);
        Licitacion::factory()->create(); // de otro responsable

        $this->assertCount(2, $responsable->licitaciones);
    }

    public function test_eliminar_responsable_no_elimina_licitaciones_asociadas(): void
    {
        $responsable = Responsable::factory()->create();
        $licitacion  = Licitacion::factory()->create(['responsable_id' => $responsable->id]);

        $responsable->delete();

        $this->assertDatabaseHas('licitaciones', [
            'codigo_licitacion' => $licitacion->codigo_licitacion,
        ]);
        $this->assertDatabaseMissing('responsables', [
            'id' => $responsable->id,
        ]);
    }
}
