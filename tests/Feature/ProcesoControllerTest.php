<?php

namespace Tests\Feature;

use App\Models\Proceso;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProcesoControllerTest extends TestCase
{
    use RefreshDatabase;

    // ─────────────────────────────────────────
    //  Admin: acceso completo
    // ─────────────────────────────────────────

    public function test_admin_puede_ver_index(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('procesos.index'))
            ->assertStatus(200)
            ->assertSee('Procesos');
    }

    public function test_admin_puede_ver_crear_formulario(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('procesos.create'))
            ->assertStatus(200)
            ->assertSee('Nuevo Proceso');
    }

    public function test_admin_puede_crear_proceso(): void
    {
        $admin = User::factory()->admin()->create();

        $data = [
            'objeto'       => 'Test de creación',
            'actividad'    => 'Actividad de prueba',
            'descripcion'  => 'Descripción de prueba',
            'fecha_inicio' => '2026-07-01',
            'hora_inicio'  => '09:00',
            'fecha_cierre' => '2026-08-15',
            'hora_cierre'  => '17:00',
            'presupuesto'  => 100000000,
            'moneda'       => 'COP',
        ];

        $this->actingAs($admin)
            ->post(route('procesos.store'), $data)
            ->assertRedirect();

        $this->assertDatabaseHas('procesos', [
            'objeto'      => 'Test de creación',
            'presupuesto' => 100000000,
            'moneda'      => 'COP',
        ]);
    }

    public function test_admin_puede_ver_detalle(): void
    {
        $admin = User::factory()->admin()->create();
        $proceso = Proceso::factory()->create();

        $this->actingAs($admin)
            ->get(route('procesos.show', $proceso))
            ->assertStatus(200)
            ->assertSee($proceso->objeto);
    }

    public function test_admin_puede_ver_editar_formulario(): void
    {
        $admin = User::factory()->admin()->create();
        $proceso = Proceso::factory()->create();

        $this->actingAs($admin)
            ->get(route('procesos.edit', $proceso))
            ->assertStatus(200)
            ->assertSee('Editar Proceso');
    }

    public function test_admin_puede_actualizar_proceso(): void
    {
        $admin = User::factory()->admin()->create();
        $proceso = Proceso::factory()->create();

        $this->actingAs($admin)
            ->put(route('procesos.update', $proceso), [
                'objeto'       => 'Objeto actualizado',
                'actividad'    => $proceso->actividad,
                'fecha_inicio' => $proceso->fecha_inicio,
                'hora_inicio'  => $proceso->hora_inicio,
                'fecha_cierre' => $proceso->fecha_cierre,
                'hora_cierre'  => $proceso->hora_cierre,
                'presupuesto'  => $proceso->presupuesto,
                'moneda'       => $proceso->moneda,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('procesos', [
            'codigo_proceso' => $proceso->codigo_proceso,
            'objeto'         => 'Objeto actualizado',
        ]);
    }

    public function test_admin_puede_eliminar_proceso(): void
    {
        $admin = User::factory()->admin()->create();
        $proceso = Proceso::factory()->create();

        $this->actingAs($admin)
            ->delete(route('procesos.destroy', $proceso))
            ->assertRedirect(route('procesos.index'));

        $this->assertDatabaseMissing('procesos', [
            'codigo_proceso' => $proceso->codigo_proceso,
        ]);
    }

    // ─────────────────────────────────────────
    //  Visor: solo index + show
    // ─────────────────────────────────────────

    public function test_visor_puede_ver_index(): void
    {
        $visor = User::factory()->visor()->create();

        // Crear algunos procesos para que la vista tenga contenido
        Proceso::factory()->count(3)->create();

        $this->actingAs($visor)
            ->get(route('procesos.index'))
            ->assertStatus(200);
    }

    public function test_visor_no_puede_ver_crear_formulario(): void
    {
        $visor = User::factory()->visor()->create();

        $this->actingAs($visor)
            ->get(route('procesos.create'))
            ->assertStatus(403); // role:admin middleware
    }

    public function test_visor_no_puede_crear_proceso(): void
    {
        $visor = User::factory()->visor()->create();

        $this->actingAs($visor)
            ->post(route('procesos.store'), [
                'objeto'       => 'Intento no autorizado',
                'actividad'    => 'Test',
                'fecha_inicio' => '2026-07-01',
                'hora_inicio'  => '09:00',
                'fecha_cierre' => '2026-08-15',
                'hora_cierre'  => '17:00',
                'presupuesto'  => 1000,
                'moneda'       => 'COP',
            ])
            ->assertStatus(403);
    }

    public function test_visor_puede_ver_detalle(): void
    {
        $visor = User::factory()->visor()->create();
        $proceso = Proceso::factory()->create();

        $this->actingAs($visor)
            ->get(route('procesos.show', $proceso))
            ->assertStatus(200);
    }

    public function test_visor_no_puede_ver_editar_formulario(): void
    {
        $visor = User::factory()->visor()->create();
        $proceso = Proceso::factory()->create();

        $this->actingAs($visor)
            ->get(route('procesos.edit', $proceso))
            ->assertStatus(403);
    }

    public function test_visor_no_puede_actualizar_proceso(): void
    {
        $visor = User::factory()->visor()->create();
        $proceso = Proceso::factory()->create();

        $this->actingAs($visor)
            ->put(route('procesos.update', $proceso), [
                'objeto' => 'Intento no autorizado',
            ])
            ->assertStatus(403);
    }

    public function test_visor_no_puede_eliminar_proceso(): void
    {
        $visor = User::factory()->visor()->create();
        $proceso = Proceso::factory()->create();

        $this->actingAs($visor)
            ->delete(route('procesos.destroy', $proceso))
            ->assertStatus(403);
    }

    // ─────────────────────────────────────────
    //  Validación
    // ─────────────────────────────────────────

    public function test_creacion_requiere_campos_obligatorios(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('procesos.store'), [])
            ->assertSessionHasErrors(['objeto', 'fecha_inicio', 'fecha_cierre', 'presupuesto', 'moneda']);
    }

    public function test_creacion_valida_moneda(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('procesos.store'), [
                'objeto'       => 'Test',
                'actividad'    => 'Test',
                'fecha_inicio' => '2026-07-01',
                'hora_inicio'  => '09:00',
                'fecha_cierre' => '2026-08-15',
                'hora_cierre'  => '17:00',
                'presupuesto'  => 1000,
                'moneda'       => 'INVALIDA',
            ])
            ->assertSessionHasErrors('moneda');
    }

    // ─────────────────────────────────────────
    //  Index: paginación y filtros
    // ─────────────────────────────────────────

    public function test_index_pagina_correctamente(): void
    {
        $admin = User::factory()->admin()->create();
        Proceso::factory()->count(15)->create();

        $this->actingAs($admin)
            ->get(route('procesos.index'))
            ->assertStatus(200)
            ->assertSee('Anterior')
            ->assertSee('Siguiente');
    }

    public function test_index_filtra_por_moneda(): void
    {
        $admin = User::factory()->admin()->create();
        Proceso::factory()->create(['moneda' => 'COP']);
        Proceso::factory()->create(['moneda' => 'USD']);

        $this->actingAs($admin)
            ->get(route('procesos.index', ['moneda' => 'COP']))
            ->assertStatus(200);
    }
}
