<?php

namespace Database\Seeders;

use App\Models\Caja;
use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Comprobante;
use App\Models\Consulta;
use App\Models\Empresa;
use App\Models\Grooming;
use App\Models\Mascota;
use App\Models\Producto;
use App\Models\Teleconsulta;
use App\Models\User;
use App\Models\Vacuna;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

/**
 * Genera datos demo repartidos en distintas fechas (ultimos 6 meses y ultimos 7 dias)
 * para que los paneles y graficos del dashboard muestren informacion representativa.
 *
 * Ejecutar con:  php artisan db:seed --class=DemoSeeder
 * Es aditivo: correrlo mas de una vez agrega mas registros.
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $empresa = Empresa::first();
        if (! $empresa) {
            $this->command?->warn('No hay empresas. Ejecuta primero: php artisan db:seed');
            return;
        }
        $eid = $empresa->id;

        $vetIds = User::where('empresa_id', $eid)->whereIn('rol', ['veterinario', 'admin'])->pluck('id')->all() ?: [null];
        $adminId = User::where('empresa_id', $eid)->where('rol', 'admin')->value('id');
        $rand = fn (array $a) => $a[array_rand($a)];

        // ===================== 1) USUARIOS (personal) =====================
        $personal = [
            ['Dr. Luis Paredes', 'veterinario', 'Cirujano'],
            ['Dra. Elena Vargas', 'veterinario', 'Medico Veterinario'],
            ['Dr. Marco Diaz', 'veterinario', 'Dermatologo'],
            ['Karla Nunez', 'recepcion', 'Recepcion'],
            ['Diego Flores', 'recepcion', 'Recepcion'],
            ['Rosa Aguilar', 'groomer', 'Peluquera'],
            ['Ivan Castro', 'groomer', 'Peluquero'],
            ['Andrea Rojas', 'veterinario', 'Medico Veterinario'],
            ['Jose Medina', 'recepcion', 'Caja'],
            ['Sandra Lopez', 'groomer', 'Estilista'],
        ];
        foreach ($personal as $i => $p) {
            User::firstOrCreate(
                ['email' => 'demo'.($i + 1).'@vetsystem.pe'],
                ['empresa_id' => $eid, 'name' => $p[0], 'password' => Hash::make('password'), 'rol' => $p[1], 'cargo' => $p[2], 'activo' => true]
            );
        }
        $vetIds = User::where('empresa_id', $eid)->whereIn('rol', ['veterinario', 'admin'])->pluck('id')->all();
        $groomerIds = User::where('empresa_id', $eid)->whereIn('rol', ['groomer', 'admin'])->pluck('id')->all();

        // ===================== 2) CLIENTES =====================
        $duenos = [
            'Roberto Guerra', 'Patricia Leon', 'Miguel Chavez', 'Carmen Rios', 'Fernando Ponce',
            'Lucia Herrera', 'Oscar Benites', 'Valeria Campos', 'Hugo Miranda', 'Daniela Ortiz',
        ];
        $nuevosClientes = [];
        foreach ($duenos as $n) {
            $nuevosClientes[] = Cliente::create([
                'empresa_id' => $eid, 'nombre' => $n, 'telefono' => '9'.random_int(10000000, 99999999),
                'email' => strtolower(str_replace(' ', '.', $n)).'@correo.com',
                'documento' => (string) random_int(40000000, 79999999),
                'created_at' => Carbon::now()->subDays(random_int(10, 170)),
            ]);
        }
        $clienteIds = Cliente::where('empresa_id', $eid)->pluck('id')->all();

        // ===================== 3) MASCOTAS (pacientes) =====================
        $especiesMap = [
            'Perro' => ['Labrador', 'Bulldog', 'Poodle', 'Beagle', 'Pastor Aleman', 'Golden'],
            'Gato' => ['Siames', 'Persa', 'Angora', 'Bengala', 'Criollo'],
            'Ave' => ['Canario', 'Periquito', 'Loro'],
            'Conejo' => ['Mini Lop', 'Cabeza de Leon'],
        ];
        $nombresMasc = ['Bruno', 'Kira', 'Zeus', 'Maya', 'Thor', 'Lola', 'Duke', 'Frida', 'Oreo', 'Canela', 'Boby', 'Mia'];
        foreach ($nombresMasc as $nm) {
            $esp = $rand(array_keys($especiesMap));
            Mascota::create([
                'empresa_id' => $eid, 'cliente_id' => $rand($clienteIds), 'nombre' => $nm, 'especie' => $esp,
                'raza' => $rand($especiesMap[$esp]), 'sexo' => $rand(['Macho', 'Hembra']),
                'peso' => random_int(2, 40) + (random_int(0, 9) / 10),
                'fecha_nacimiento' => Carbon::now()->subMonths(random_int(4, 120)),
                'created_at' => Carbon::now()->subDays(random_int(5, 160)),
            ]);
        }
        $mascotaIds = Mascota::where('empresa_id', $eid)->pluck('id')->all();

        // ===================== 4) PRODUCTOS (inventario) =====================
        $prods = [
            ['Alimento Cachorro 3kg', 'Alimento', 34.90, 22], ['Alimento Senior 7.5kg', 'Alimento', 129.90, 105],
            ['Antibiotico Amoxicilina', 'Farmacia', 22.00, 10], ['Analgesico Veterinario', 'Farmacia', 18.50, 8],
            ['Vacuna Sextuple', 'Farmacia', 60.00, 30], ['Cepillo Deslanador', 'Accesorios', 24.90, 10],
            ['Comedero Doble Acero', 'Accesorios', 39.90, 18], ['Correa Retractil 5m', 'Accesorios', 45.00, 20],
            ['Toallitas Humedas', 'Higiene', 12.90, 5], ['Snacks Dentales', 'Alimento', 15.90, 7],
        ];
        foreach ($prods as $p) {
            Producto::create([
                'empresa_id' => $eid, 'nombre' => $p[0], 'categoria' => $p[1], 'precio' => $p[2], 'costo' => $p[3],
                'stock' => random_int(4, 60), 'stock_minimo' => random_int(3, 8), 'sku' => 'SKU-'.random_int(1000, 9999),
                'created_at' => Carbon::now()->subDays(random_int(20, 170)),
            ]);
        }
        $productos = Producto::where('empresa_id', $eid)->get();

        // ===================== 5) CITAS =====================
        $motivos = ['Consulta general', 'Vacunacion', 'Control post-operatorio', 'Desparasitacion', 'Emergencia', 'Chequeo anual'];
        $estadosCita = ['pendiente', 'confirmada', 'atendida', 'cancelada'];
        // Citas en cada uno de los ultimos 7 dias (para el grafico de actividad)
        foreach (range(0, 6) as $d) {
            foreach (range(1, random_int(1, 3)) as $x) {
                $fecha = Carbon::today()->subDays($d)->setTime(random_int(9, 18), $rand([0, 15, 30, 45]));
                Cita::create([
                    'empresa_id' => $eid, 'mascota_id' => $rand($mascotaIds), 'user_id' => $rand($vetIds),
                    'fecha' => $fecha, 'motivo' => $rand($motivos),
                    'estado' => $d === 0 ? $rand(['pendiente', 'confirmada']) : 'atendida',
                    'created_at' => $fecha,
                ]);
            }
        }
        // Citas repartidas en los ultimos 6 meses (variedad de estados)
        foreach (range(1, 24) as $x) {
            $fecha = Carbon::now()->subDays(random_int(8, 175))->setTime(random_int(9, 18), $rand([0, 30]));
            Cita::create([
                'empresa_id' => $eid, 'mascota_id' => $rand($mascotaIds), 'user_id' => $rand($vetIds),
                'fecha' => $fecha, 'motivo' => $rand($motivos), 'estado' => $rand($estadosCita), 'created_at' => $fecha,
            ]);
        }
        // Algunas citas futuras (proximas citas del dashboard)
        foreach (range(1, 6) as $x) {
            $fecha = Carbon::now()->addDays(random_int(1, 10))->setTime(random_int(9, 18), $rand([0, 30]));
            Cita::create([
                'empresa_id' => $eid, 'mascota_id' => $rand($mascotaIds), 'user_id' => $rand($vetIds),
                'fecha' => $fecha, 'motivo' => $rand($motivos), 'estado' => 'pendiente', 'created_at' => Carbon::now(),
            ]);
        }

        // ===================== 6) HISTORIA CLINICA (consultas) =====================
        $dx = ['Paciente sano', 'Otitis externa', 'Gastroenteritis leve', 'Dermatitis alergica', 'Control de peso', 'Parasitosis'];
        foreach (range(1, 12) as $x) {
            $fecha = Carbon::now()->subDays(random_int(2, 175));
            Consulta::create([
                'empresa_id' => $eid, 'mascota_id' => $rand($mascotaIds), 'user_id' => $rand($vetIds), 'fecha' => $fecha,
                'motivo' => $rand($motivos), 'sintomas' => 'Evaluacion clinica y anamnesis.', 'diagnostico' => $rand($dx),
                'tratamiento' => 'Indicaciones y medicacion segun cuadro.', 'peso' => random_int(3, 38) + 0.4,
                'temperatura' => 37.5 + (random_int(0, 20) / 10), 'created_at' => $fecha,
            ]);
        }

        // ===================== 7) VACUNAS =====================
        $vacNombres = ['Antirrabica', 'Sextuple', 'Triple Felina', 'Parvovirus', 'Moquillo', 'Leptospirosis', 'Bordetella'];
        foreach (range(1, 12) as $x) {
            $aplica = Carbon::now()->subDays(random_int(5, 170));
            Vacuna::create([
                'empresa_id' => $eid, 'mascota_id' => $rand($mascotaIds), 'user_id' => $rand($vetIds),
                'nombre' => $rand($vacNombres), 'fecha_aplicacion' => $aplica,
                'proxima_dosis' => (clone $aplica)->addDays(random_int(20, 200)), 'lote' => 'L'.random_int(1000, 9999),
                'created_at' => $aplica,
            ]);
        }

        // ===================== 8) FACTURACION (comprobantes repartidos en 6 meses) =====================
        $numero = (int) Comprobante::where('empresa_id', $eid)->where('serie', 'B001')->max('numero');
        for ($mesAtras = 5; $mesAtras >= 0; $mesAtras--) {
            $base = Carbon::now()->subMonths($mesAtras);
            $diasEnMes = $mesAtras === 0 ? Carbon::now()->day : $base->daysInMonth;
            foreach (range(1, random_int(4, 7)) as $x) {
                $fecha = $base->copy()->day(random_int(1, max(1, $diasEnMes)))->setTime(random_int(9, 19), $rand([0, 30]));
                $lineas = random_int(1, 3);
                $subtotal = 0;
                $items = [];
                foreach (range(1, $lineas) as $l) {
                    $prod = $productos->random();
                    $cant = random_int(1, 3);
                    $imp = round($prod->precio * $cant, 2);
                    $subtotal += $imp;
                    $items[] = ['producto_id' => $prod->id, 'descripcion' => $prod->nombre, 'cantidad' => $cant, 'precio_unitario' => $prod->precio, 'importe' => $imp];
                }
                $igv = round($subtotal * 0.18, 2);
                $comp = Comprobante::create([
                    'empresa_id' => $eid, 'cliente_id' => $rand($clienteIds), 'user_id' => $adminId,
                    'tipo' => $rand(['ticket', 'boleta', 'factura']), 'serie' => 'B001', 'numero' => ++$numero,
                    'fecha' => $fecha, 'subtotal' => $subtotal, 'igv' => $igv, 'total' => round($subtotal + $igv, 2),
                    'metodo_pago' => $rand(['efectivo', 'tarjeta', 'yape', 'transferencia']), 'estado' => 'emitido',
                    'created_at' => $fecha,
                ]);
                foreach ($items as $it) {
                    $comp->items()->create($it);
                }
            }
        }

        // ===================== 9) PELUQUERIA (groomings) =====================
        $servicios = ['Bano completo', 'Bano y corte', 'Corte de pelo', 'Corte de unas', 'Spa completo', 'Deslanado'];
        foreach (range(1, 12) as $x) {
            $fecha = Carbon::now()->subDays(random_int(0, 160))->setTime(random_int(9, 18), $rand([0, 30]));
            Grooming::create([
                'empresa_id' => $eid, 'mascota_id' => $rand($mascotaIds), 'user_id' => $rand($groomerIds ?: [$adminId]),
                'fecha' => $fecha, 'servicio' => $rand($servicios), 'precio' => $rand([25, 35, 45, 60, 80]),
                'estado' => $fecha->isFuture() ? 'programado' : $rand(['completado', 'completado', 'cancelado']),
                'created_at' => $fecha,
            ]);
        }

        // ===================== 10) TELEMEDICINA (teleconsultas) =====================
        foreach (range(1, 12) as $x) {
            $fecha = Carbon::now()->subDays(random_int(-8, 150))->setTime(random_int(9, 18), $rand([0, 30]));
            Teleconsulta::create([
                'empresa_id' => $eid, 'mascota_id' => $rand($mascotaIds), 'user_id' => $rand($vetIds),
                'fecha' => $fecha, 'motivo' => $rand($motivos), 'enlace' => 'https://meet.google.com/'.substr(md5((string) $x), 0, 10),
                'estado' => $fecha->isFuture() ? 'programada' : $rand(['realizada', 'realizada', 'cancelada']),
                'created_at' => $fecha,
            ]);
        }

        // ===================== 11) CAJA (historial de cierres + movimientos) =====================
        foreach (range(1, 4) as $x) {
            $apertura = Carbon::now()->subDays($x * 7)->setTime(8, 0);
            $caja = Caja::create([
                'empresa_id' => $eid, 'user_id' => $adminId, 'fecha_apertura' => $apertura, 'monto_apertura' => 100,
                'estado' => 'cerrada', 'fecha_cierre' => (clone $apertura)->setTime(19, 0), 'created_at' => $apertura,
            ]);
            $ing = 0;
            foreach (range(1, random_int(3, 6)) as $m) {
                $monto = $rand([50, 80, 120, 150, 200, 45]);
                $ing += $monto;
                $caja->movimientos()->create(['user_id' => $adminId, 'tipo' => 'ingreso', 'concepto' => 'Venta del dia', 'monto' => $monto]);
            }
            $caja->movimientos()->create(['user_id' => $adminId, 'tipo' => 'egreso', 'concepto' => 'Compra de insumos', 'monto' => $rand([30, 45, 60])]);
            $caja->update(['monto_cierre' => 100 + $ing]);
        }

        $this->command?->info('Datos demo generados y repartidos en fechas correctamente.');
    }
}
