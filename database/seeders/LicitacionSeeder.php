<?php

namespace Database\Seeders;

use App\Models\Licitacion;
use App\Models\Responsable;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LicitacionSeeder extends Seeder
{
    public function run(): void
    {
        $admin    = User::where('role', 'admin')->first();
        $adminId  = $admin?->id;

        $responsables = Responsable::all();
        if ($responsables->isEmpty()) {
            $this->command->warn('No hay responsables para asignar. Ejecutá primero ResponsableSeeder.');
            return;
        }

        $procesos = [
            [
                'objeto'       => 'Adquisición de equipos de cómputo',
                'actividad'    => 'Oficina de Sistemas',
                'descripcion'  => 'Compra de 50 estaciones de trabajo, 10 laptops y servidores para la sede principal. Incluye software base y garantía por 3 años.',
                'moneda'       => 'COP',
                'presupuesto'  => 450000000,
                'estado'       => 'Adjudicado',
                'fecha_inicio' => '2026-01-15',
                'hora_inicio'  => '08:00',
                'fecha_cierre' => '2026-02-15',
                'hora_cierre'  => '17:00',
            ],
            [
                'objeto'       => 'Mantenimiento de infraestructura eléctrica',
                'actividad'    => 'Dirección Administrativa',
                'descripcion'  => 'Mantenimiento preventivo y correctivo de las instalaciones eléctricas en las 3 sedes de la entidad. Incluye materiales y mano de obra.',
                'moneda'       => 'COP',
                'presupuesto'  => 180000000,
                'estado'       => 'En evaluación',
                'fecha_inicio' => '2026-03-01',
                'hora_inicio'  => '09:00',
                'fecha_cierre' => '2026-03-30',
                'hora_cierre'  => '15:00',
            ],
            [
                'objeto'       => 'Servicio de vigilancia privada',
                'actividad'    => 'Gestión Humana',
                'descripcion'  => 'Servicio de vigilancia y seguridad privada para las instalaciones de la entidad. Turnos 24/7 incluyendo festivos. Personal capacitado y uniformado.',
                'moneda'       => 'COP',
                'presupuesto'  => 320000000,
                'estado'       => 'Publicado',
                'fecha_inicio' => '2026-04-01',
                'hora_inicio'  => '08:00',
                'fecha_cierre' => '2026-04-28',
                'hora_cierre'  => '17:00',
            ],
            [
                'objeto'       => 'Consultoría en transformación digital',
                'actividad'    => 'Oficina de Planeación',
                'descripcion'  => 'Consultoría especializada para la implementación de la estrategia de transformación digital. Diagnóstico, hoja de ruta y acompañamiento por 6 meses.',
                'moneda'       => 'USD',
                'presupuesto'  => 85000,
                'estado'       => 'Borrador',
                'fecha_inicio' => '2026-05-01',
                'hora_inicio'  => '09:00',
                'fecha_cierre' => '2026-05-30',
                'hora_cierre'  => '18:00',
            ],
            [
                'objeto'       => 'Suministro de papelería y útiles de oficina',
                'actividad'    => 'Almacén e Inventarios',
                'descripcion'  => 'Suministro de papelería, tintas, tóner y útiles de oficina para todas las dependencias. Contrato por demanda con entregas parciales.',
                'moneda'       => 'COP',
                'presupuesto'  => 95000000,
                'estado'       => 'Cancelado',
                'fecha_inicio' => '2026-02-01',
                'hora_inicio'  => '08:00',
                'fecha_cierre' => '2026-02-20',
                'hora_cierre'  => '17:00',
            ],
            [
                'objeto'       => 'Desarrollo de plataforma web',
                'actividad'    => 'Oficina de Sistemas',
                'descripcion'  => 'Desarrollo de plataforma web para la gestión de trámites internos. Módulos: usuarios, solicitudes, seguimiento y reportes. Stack Laravel + Vue.js.',
                'moneda'       => 'COP',
                'presupuesto'  => 250000000,
                'estado'       => 'Publicado',
                'fecha_inicio' => '2026-06-01',
                'hora_inicio'  => '09:00',
                'fecha_cierre' => '2026-07-01',
                'hora_cierre'  => '17:00',
            ],
            [
                'objeto'       => 'Adecuación física sede administrativa',
                'actividad'    => 'Dirección Administrativa',
                'descripcion'  => 'Remodelación de las oficinas del primer piso: cisne falso, pisos, divisiones en drywall, pintura e instalación de aire acondicionado.',
                'moneda'       => 'COP',
                'presupuesto'  => 410000000,
                'estado'       => 'En evaluación',
                'fecha_inicio' => '2026-03-15',
                'hora_inicio'  => '08:00',
                'fecha_cierre' => '2026-04-15',
                'hora_cierre'  => '16:00',
            ],
            [
                'objeto'       => 'Software antivirus corporativo',
                'actividad'    => 'Oficina de Sistemas',
                'descripcion'  => 'Licenciamiento de software antivirus para 500 equipos por 2 años. Incluye consola de administración centralizada y soporte técnico.',
                'moneda'       => 'USD',
                'presupuesto'  => 45000,
                'estado'       => 'Borrador',
                'fecha_inicio' => '2026-07-01',
                'hora_inicio'  => '09:00',
                'fecha_cierre' => '2026-07-20',
                'hora_cierre'  => '17:00',
            ],
            [
                'objeto'       => 'Servicio de cafetería y alimentación',
                'actividad'    => 'Bienestar Social',
                'descripcion'  => 'Servicio de cafetería para empleados y contratistas. Incluye desayunos, almuerzos y café permanente. Capacidad para 200 personas/día.',
                'moneda'       => 'COP',
                'presupuesto'  => 180000000,
                'estado'       => 'Adjudicado',
                'fecha_inicio' => '2026-01-01',
                'hora_inicio'  => '07:00',
                'fecha_cierre' => '2026-01-20',
                'hora_cierre'  => '17:00',
            ],
            [
                'objeto'       => 'Capacitación en contratación pública',
                'actividad'    => 'Gestión Humana',
                'descripcion'  => 'Programa de capacitación en contratación pública para 30 funcionarios. Modalidad virtual con sesiones en vivo. Certificación internacional.',
                'moneda'       => 'EUR',
                'presupuesto'  => 28000,
                'estado'       => 'Cancelado',
                'fecha_inicio' => '2026-05-01',
                'hora_inicio'  => '08:00',
                'fecha_cierre' => '2026-05-15',
                'hora_cierre'  => '18:00',
            ],
        ];

        $count = 0;
        foreach ($procesos as $data) {
            $data['creador_id']     = $adminId;
            $data['responsable_id'] = $responsables->random()->id;

            Licitacion::firstOrCreate(
                ['objeto' => $data['objeto']],
                $data
            );
            $count++;
        }

        $this->command->info("Licitaciones creadas: {$count}");
    }
}
