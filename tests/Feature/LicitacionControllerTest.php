<?php

namespace Tests\Feature;

use App\Models\Licitacion;
use App\Models\Responsable;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LicitacionControllerTest extends TestCase
{
    use RefreshDatabase;

    // ─────────────────────────────────────────
    //  Admin: acceso completo
    // ─────────────────────────────────────────

    public function test_admin_puede_ver_index(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('licitaciones.index'))
            ->assertStatus(200)
            ->assertSee('Consultar');
    }

    public function test_admin_ve_lista_vacia_cuando_no_hay_licitaciones(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('licitaciones.index'))
            ->assertStatus(200)
            ->assertSee('No se encontraron licitaciones');
    }

    public function test_admin_puede_ver_crear_formulario(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('licitaciones.create'))
            ->assertStatus(200)
            ->assertSee('Nueva Licitación');
    }

    public function test_admin_puede_crear_licitacion(): void
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
            ->post(route('licitaciones.store'), $data)
            ->assertRedirect();

        $this->assertDatabaseHas('licitaciones', [
            'objeto'      => 'Test de creación',
            'presupuesto' => 100000000,
            'moneda'      => 'COP',
            'estado'      => 'Borrador',
            'creador_id'  => $admin->id,
        ]);
    }

    public function test_admin_puede_crear_licitacion_con_responsable(): void
    {
        $admin       = User::factory()->admin()->create();
        $responsable = Responsable::factory()->create();

        $data = [
            'objeto'         => 'Con responsable',
            'actividad'      => 'Test',
            'descripcion'    => 'Licitación con responsable asignado',
            'fecha_inicio'   => '2026-07-01',
            'hora_inicio'    => '09:00',
            'fecha_cierre'   => '2026-08-15',
            'hora_cierre'    => '17:00',
            'presupuesto'    => 50000000,
            'moneda'         => 'USD',
            'responsable_id' => $responsable->id,
        ];

        $this->actingAs($admin)
            ->post(route('licitaciones.store'), $data)
            ->assertRedirect();

        $this->assertDatabaseHas('licitaciones', [
            'objeto'         => 'Con responsable',
            'responsable_id' => $responsable->id,
        ]);
    }

    public function test_admin_puede_crear_licitacion_con_estado_explicito(): void
    {
        $admin = User::factory()->admin()->create();

        $data = [
            'objeto'       => 'Licitación pública',
            'actividad'    => 'Test',
            'descripcion'  => 'Publicada desde el inicio',
            'fecha_inicio' => '2026-07-01',
            'hora_inicio'  => '09:00',
            'fecha_cierre' => '2026-08-15',
            'hora_cierre'  => '17:00',
            'presupuesto'  => 100000000,
            'moneda'       => 'COP',
            'estado'       => 'Publicado',
        ];

        $this->actingAs($admin)
            ->post(route('licitaciones.store'), $data)
            ->assertRedirect();

        $this->assertDatabaseHas('licitaciones', [
            'objeto' => 'Licitación pública',
            'estado' => 'Publicado',
        ]);
    }

    public function test_admin_puede_ver_detalle(): void
    {
        $admin      = User::factory()->admin()->create();
        $licitacion = Licitacion::factory()->create();

        $this->actingAs($admin)
            ->get(route('licitaciones.show', $licitacion))
            ->assertStatus(200)
            ->assertSee($licitacion->objeto);
    }

    public function test_admin_puede_ver_editar_formulario(): void
    {
        $admin      = User::factory()->admin()->create();
        $licitacion = Licitacion::factory()->create();

        $this->actingAs($admin)
            ->get(route('licitaciones.edit', $licitacion))
            ->assertStatus(200)
            ->assertSee('Editar Licitación');
    }

    public function test_admin_puede_actualizar_licitacion(): void
    {
        $admin      = User::factory()->admin()->create();
        $licitacion = Licitacion::factory()->create();

        $this->actingAs($admin)
            ->put(route('licitaciones.update', $licitacion), [
                'objeto'       => 'Objeto actualizado',
                'descripcion'  => $licitacion->descripcion,
                'actividad'    => $licitacion->actividad,
                'fecha_inicio' => $licitacion->fecha_inicio,
                'hora_inicio'  => $licitacion->hora_inicio,
                'fecha_cierre' => $licitacion->fecha_cierre,
                'hora_cierre'  => $licitacion->hora_cierre,
                'presupuesto'  => $licitacion->presupuesto,
                'moneda'       => $licitacion->moneda,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('licitaciones', [
            'codigo_licitacion' => $licitacion->codigo_licitacion,
            'objeto'            => 'Objeto actualizado',
        ]);
    }

    public function test_admin_puede_actualizar_estado_de_licitacion(): void
    {
        $admin      = User::factory()->admin()->create();
        $licitacion = Licitacion::factory()->create(['estado' => 'Borrador']);

        $this->actingAs($admin)
            ->put(route('licitaciones.update', $licitacion), [
                'objeto'       => $licitacion->objeto,
                'descripcion'  => $licitacion->descripcion,
                'actividad'    => $licitacion->actividad,
                'fecha_inicio' => $licitacion->fecha_inicio,
                'hora_inicio'  => $licitacion->hora_inicio,
                'fecha_cierre' => $licitacion->fecha_cierre,
                'hora_cierre'  => $licitacion->hora_cierre,
                'presupuesto'  => $licitacion->presupuesto,
                'moneda'       => $licitacion->moneda,
                'estado'       => 'Adjudicado',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('licitaciones', [
            'codigo_licitacion' => $licitacion->codigo_licitacion,
            'estado'            => 'Adjudicado',
        ]);
    }

    public function test_admin_puede_eliminar_licitacion(): void
    {
        $admin      = User::factory()->admin()->create();
        $licitacion = Licitacion::factory()->create();

        $this->actingAs($admin)
            ->delete(route('licitaciones.destroy', $licitacion))
            ->assertRedirect(route('licitaciones.index'));

        $this->assertDatabaseMissing('licitaciones', [
            'codigo_licitacion' => $licitacion->codigo_licitacion,
        ]);
    }

    // ─────────────────────────────────────────
    //  Visor: solo index + show
    // ─────────────────────────────────────────

    public function test_visor_puede_ver_index_con_listado(): void
    {
        $visor = User::factory()->visor()->create();

        Licitacion::factory()->create(['objeto' => 'Visible para visores']);

        $this->actingAs($visor)
            ->get(route('licitaciones.index'))
            ->assertStatus(200)
            ->assertSee('Visible para visores');
    }

    public function test_visor_no_puede_ver_crear_formulario(): void
    {
        $visor = User::factory()->visor()->create();

        $this->actingAs($visor)
            ->get(route('licitaciones.create'))
            ->assertStatus(403);
    }

    public function test_visor_no_puede_crear_licitacion(): void
    {
        $visor = User::factory()->visor()->create();

        $this->actingAs($visor)
            ->post(route('licitaciones.store'), [
                'objeto'       => 'Intento no autorizado',
                'descripcion'  => 'Test',
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
        $visor      = User::factory()->visor()->create();
        $licitacion = Licitacion::factory()->create(['objeto' => 'Detalle visible para visor']);

        $this->actingAs($visor)
            ->get(route('licitaciones.show', $licitacion))
            ->assertStatus(200)
            ->assertSee('Detalle visible para visor');
    }

    public function test_visor_no_puede_ver_editar_formulario(): void
    {
        $visor      = User::factory()->visor()->create();
        $licitacion = Licitacion::factory()->create();

        $this->actingAs($visor)
            ->get(route('licitaciones.edit', $licitacion))
            ->assertStatus(403);
    }

    public function test_visor_no_puede_actualizar_licitacion(): void
    {
        $visor      = User::factory()->visor()->create();
        $licitacion = Licitacion::factory()->create();

        $this->actingAs($visor)
            ->put(route('licitaciones.update', $licitacion), [
                'objeto' => 'Intento no autorizado',
            ])
            ->assertStatus(403);
    }

    public function test_visor_no_puede_eliminar_licitacion(): void
    {
        $visor      = User::factory()->visor()->create();
        $licitacion = Licitacion::factory()->create();

        $this->actingAs($visor)
            ->delete(route('licitaciones.destroy', $licitacion))
            ->assertStatus(403);
    }

    // ─────────────────────────────────────────
    //  Validación
    // ─────────────────────────────────────────

    public function test_creacion_requiere_campos_obligatorios(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('licitaciones.store'), [])
            ->assertSessionHasErrors([
                'objeto', 'descripcion',
                'fecha_inicio', 'hora_inicio',
                'fecha_cierre', 'hora_cierre',
                'presupuesto', 'moneda',
            ]);
    }

    public function test_creacion_valida_moneda(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('licitaciones.store'), [
                'objeto'       => 'Test',
                'descripcion'  => 'Test',
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

    public function test_creacion_valida_fecha_inicio_no_mayor_a_fecha_cierre(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('licitaciones.store'), [
                'objeto'       => 'Fechas inválidas',
                'descripcion'  => 'Test',
                'actividad'    => 'Test',
                'fecha_inicio' => '2026-08-15',
                'hora_inicio'  => '09:00',
                'fecha_cierre' => '2026-07-01',
                'hora_cierre'  => '17:00',
                'presupuesto'  => 1000,
                'moneda'       => 'COP',
            ])
            ->assertSessionHasErrors(['fecha_inicio', 'fecha_cierre']);
    }

    public function test_creacion_valida_presupuesto_no_negativo(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('licitaciones.store'), [
                'objeto'       => 'Presupuesto negativo',
                'descripcion'  => 'Test',
                'actividad'    => 'Test',
                'fecha_inicio' => '2026-07-01',
                'hora_inicio'  => '09:00',
                'fecha_cierre' => '2026-08-15',
                'hora_cierre'  => '17:00',
                'presupuesto'  => -100,
                'moneda'       => 'COP',
            ])
            ->assertSessionHasErrors('presupuesto');
    }

    public function test_creacion_valida_estado_no_invalido(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('licitaciones.store'), [
                'objeto'       => 'Estado inválido',
                'descripcion'  => 'Test',
                'actividad'    => 'Test',
                'fecha_inicio' => '2026-07-01',
                'hora_inicio'  => '09:00',
                'fecha_cierre' => '2026-08-15',
                'hora_cierre'  => '17:00',
                'presupuesto'  => 1000,
                'moneda'       => 'COP',
                'estado'       => 'EstadoInexistente',
            ])
            ->assertSessionHasErrors('estado');
    }

    // ─────────────────────────────────────────
    //  Index: paginación y filtros
    // ─────────────────────────────────────────

    public function test_index_muestra_paginacion_cuando_hay_mas_de_10(): void
    {
        $admin = User::factory()->admin()->create();
        Licitacion::factory()->count(25)->create();

        $this->actingAs($admin)
            ->get(route('licitaciones.index'))
            ->assertStatus(200)
            ->assertSee('Siguiente');  // página 1 con 25 items → hay siguiente
    }

    public function test_index_pagina_2_muestra_anterior_y_siguiente(): void
    {
        $admin = User::factory()->admin()->create();
        Licitacion::factory()->count(25)->create();

        $this->actingAs($admin)
            ->get(route('licitaciones.index', ['page' => 2]))
            ->assertStatus(200)
            ->assertSee('Anterior')
            ->assertSee('Siguiente');
    }

    public function test_index_filtra_por_estado(): void
    {
        $admin = User::factory()->admin()->create();
        Licitacion::factory()->create(['estado' => 'Publicado', 'objeto' => 'Licitación pública']);
        Licitacion::factory()->create(['estado' => 'Borrador', 'objeto' => 'Licitación borrador']);

        $this->actingAs($admin)
            ->get(route('licitaciones.index', ['estado' => 'Publicado']))
            ->assertStatus(200)
            ->assertSee('Licitación pública')
            ->assertDontSee('Licitación borrador');
    }

    public function test_index_filtra_por_search(): void
    {
        $admin = User::factory()->admin()->create();
        Licitacion::factory()->create(['objeto' => 'Mantenimiento de vías']);
        Licitacion::factory()->create(['objeto' => 'Compra de computadores']);

        $this->actingAs($admin)
            ->get(route('licitaciones.index', ['search' => 'Mantenimiento']))
            ->assertStatus(200)
            ->assertSee('Mantenimiento de vías')
            ->assertDontSee('Compra de computadores');
    }

    public function test_index_filtra_por_codigo(): void
    {
        $admin = User::factory()->admin()->create();
        $l1    = Licitacion::factory()->create(['objeto' => 'Primera']);
        $l2    = Licitacion::factory()->create(['objeto' => 'Segunda']);

        $this->actingAs($admin)
            ->get(route('licitaciones.index', ['codigo' => $l1->codigo_licitacion]))
            ->assertStatus(200)
            ->assertSee('Primera')
            ->assertDontSee('Segunda');
    }

    public function test_index_filtros_combinados_estado_y_search(): void
    {
        $admin = User::factory()->admin()->create();
        Licitacion::factory()->create(['estado' => 'Publicado', 'objeto' => 'Construcción de puente']);
        Licitacion::factory()->create(['estado' => 'Publicado', 'objeto' => 'Mantenimiento vial']);
        Licitacion::factory()->create(['estado' => 'Borrador', 'objeto' => 'Construcción de escuela']);

        $this->actingAs($admin)
            ->get(route('licitaciones.index', ['estado' => 'Publicado', 'search' => 'Construcción']))
            ->assertStatus(200)
            ->assertSee('Construcción de puente')
            ->assertDontSee('Mantenimiento vial')
            ->assertDontSee('Construcción de escuela');
    }

    public function test_index_mensaje_cuando_filtro_no_tiene_resultados(): void
    {
        $admin = User::factory()->admin()->create();
        Licitacion::factory()->create(['estado' => 'Publicado']);

        $this->actingAs($admin)
            ->get(route('licitaciones.index', ['estado' => 'Adjudicado']))
            ->assertStatus(200)
            ->assertSee('No se encontraron licitaciones.');
    }

    // ─────────────────────────────────────────
    //  Export CSV
    // ─────────────────────────────────────────

    public function test_admin_puede_exportar_csv(): void
    {
        $admin = User::factory()->admin()->create();
        Licitacion::factory()->count(3)->create();

        $this->actingAs($admin)
            ->get(route('licitaciones.export'))
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_visor_puede_exportar_csv(): void
    {
        $visor = User::factory()->visor()->create();
        Licitacion::factory()->count(3)->create();

        $this->actingAs($visor)
            ->get(route('licitaciones.export'))
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_export_csv_incluye_datos_de_licitaciones(): void
    {
        $admin = User::factory()->admin()->create();
        Licitacion::factory()->create([
            'objeto'  => 'Para exportar',
            'moneda'  => 'EUR',
            'estado'  => 'Publicado',
        ]);

        $response = $this->actingAs($admin)->get(route('licitaciones.export'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();
        $this->assertStringContainsString('Para exportar', $content);
        $this->assertStringContainsString('Publicado', $content);
        $this->assertStringContainsString('EUR', $content);
    }
}
