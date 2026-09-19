<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Comprobante;
use App\Models\Configuracion;
use App\Models\Consulta;
use App\Models\Empresa;
use App\Models\Mascota;
use App\Models\Plan;
use App\Models\Producto;
use App\Models\SuscripcionPago;
use App\Models\User;
use App\Models\Vacuna;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ----- Planes de suscripcion -----
        $free = Plan::firstOrCreate(['nombre' => 'Free'], [
            'precio' => 0, 'periodo' => 'mensual', 'max_usuarios' => 2, 'max_pacientes' => 50,
            'caracteristicas' => "1 sucursal\nModulos basicos\nSoporte por correo", 'activo' => true,
        ]);
        $pro = Plan::firstOrCreate(['nombre' => 'Pro'], [
            'precio' => 99.00, 'periodo' => 'mensual', 'max_usuarios' => 10, 'max_pacientes' => 1000,
            'caracteristicas' => "Todos los modulos\nFacturacion electronica\nReportes y BI\nSoporte prioritario", 'activo' => true,
        ]);
        Plan::firstOrCreate(['nombre' => 'Premium'], [
            'precio' => 199.00, 'periodo' => 'mensual', 'max_usuarios' => 0, 'max_pacientes' => 0,
            'caracteristicas' => "Usuarios ilimitados\nPacientes ilimitados\nMultisucursal\nSoporte dedicado", 'activo' => true,
        ]);

        // ----- Empresa por defecto (la clinica existente) -----
        $empresa = Empresa::firstOrCreate(['nombre' => 'VetSystem Clinica Veterinaria'], [
            'ruc' => '20123456789', 'email' => 'contacto@vetsystem.pe', 'telefono' => '(01) 555-1234',
            'direccion' => 'Av. Los Animales 123, Lima', 'plan_id' => $pro->id, 'estado' => 'activa',
            'fecha_inicio' => Carbon::today(), 'fecha_vencimiento' => Carbon::today()->addYear(),
        ]);

        // ----- Backfill: asigna la empresa por defecto a datos existentes sin empresa -----
        foreach (['clientes', 'mascotas', 'citas', 'productos', 'consultas', 'vacunas', 'comprobantes', 'cajas', 'groomings', 'teleconsultas', 'configuraciones'] as $tabla) {
            DB::table($tabla)->whereNull('empresa_id')->update(['empresa_id' => $empresa->id]);
        }
        DB::table('users')->whereNull('empresa_id')->where('es_super_admin', false)->update(['empresa_id' => $empresa->id]);

        // ----- Super Admin de la plataforma -----
        User::updateOrCreate(['email' => 'superadmin@example.com'], [
            'empresa_id' => null, 'name' => 'Super Administrador', 'password' => Hash::make('password'),
            'rol' => 'admin', 'es_super_admin' => true, 'cargo' => 'Plataforma', 'activo' => true,
        ]);

        // ----- Configuracion de la clinica -----
        Configuracion::firstOrCreate(['empresa_id' => $empresa->id], [
            'nombre_clinica' => $empresa->nombre, 'ruc' => $empresa->ruc, 'direccion' => $empresa->direccion,
            'telefono' => $empresa->telefono, 'email' => $empresa->email, 'moneda' => 'S/', 'igv_porcentaje' => 18,
        ]);

        // ----- Usuarios de la clinica -----
        User::updateOrCreate(['email' => 'admin@example.com'], [
            'empresa_id' => $empresa->id, 'name' => 'Administrador', 'password' => Hash::make('password'),
            'rol' => 'admin', 'cargo' => 'Director de Clinica', 'activo' => true,
        ]);
        User::updateOrCreate(['email' => 'vet@example.com'], [
            'empresa_id' => $empresa->id, 'name' => 'Dra. Carla Mendoza', 'password' => Hash::make('password'),
            'rol' => 'veterinario', 'cargo' => 'Medico Veterinario', 'activo' => true,
        ]);
        User::updateOrCreate(['email' => 'recepcion@example.com'], [
            'empresa_id' => $empresa->id, 'name' => 'Lucia Rojas', 'password' => Hash::make('password'),
            'rol' => 'recepcion', 'cargo' => 'Recepcion', 'activo' => true,
        ]);

        // ----- Empresas demo adicionales (poblan el panel de plataforma) -----
        if (Empresa::count() < 2) {
            $planFree = Plan::where('nombre', 'Free')->first();
            $planPro = Plan::where('nombre', 'Pro')->first();
            $planPremium = Plan::where('nombre', 'Premium')->first();
            $demoEmpresas = [
                ['Patitas Felices', 'prueba', $planFree, Carbon::today()->addDays(9), 'ana.torres'],
                ['Animal Care Center', 'activa', $planPremium, Carbon::today()->addMonths(1), 'carlos.vet'],
                ['Clinica San Roque', 'activa', $planPro, Carbon::today()->addDays(20), 'roque.admin'],
                ['Mascotas Sanas EIRL', 'suspendida', $planFree, Carbon::today()->subDays(6), 'mario.admin'],
            ];
            foreach ($demoEmpresas as $de) {
                $emp = Empresa::create([
                    'nombre' => $de[0], 'estado' => $de[1], 'plan_id' => optional($de[2])->id,
                    'ruc' => (string) random_int(20100000000, 20999999999),
                    'fecha_inicio' => Carbon::today()->subMonths(random_int(1, 6)), 'fecha_vencimiento' => $de[3],
                    'email' => $de[4].'@correo.com', 'telefono' => '9'.random_int(10000000, 99999999),
                ]);
                User::updateOrCreate(['email' => $de[4].'@vetsystem.pe'], [
                    'empresa_id' => $emp->id, 'name' => ucwords(str_replace('.', ' ', $de[4])),
                    'password' => Hash::make('password'), 'rol' => 'admin', 'cargo' => 'Administrador', 'activo' => true,
                ]);
                // Pago de suscripcion para las empresas activas
                if ($de[1] === 'activa' && $de[2]) {
                    SuscripcionPago::create([
                        'empresa_id' => $emp->id, 'plan_id' => $de[2]->id, 'monto' => $de[2]->precio,
                        'periodo' => 'mensual', 'metodo' => 'transferencia', 'fecha_pago' => Carbon::today()->subDays(random_int(1, 25)),
                        'fecha_inicio' => Carbon::today()->subMonth(), 'fecha_fin' => $de[3], 'estado' => 'pagado',
                    ]);
                }
            }
        }

        $eid = $empresa->id;

        // ----- Clientes y mascotas demo (solo primera vez) -----
        if (Cliente::withoutGlobalScope('empresa')->count() === 0) {
            $duenos = [
                ['Maria Fernandez', '987654321', 'maria@correo.com'], ['Jorge Salinas', '981112233', 'jorge@correo.com'],
                ['Ana Torres', '944556677', 'ana@correo.com'], ['Pedro Ramirez', '933221100', 'pedro@correo.com'],
                ['Sofia Castillo', '922334455', 'sofia@correo.com'],
            ];
            $mascotasDemo = [
                ['Firulais', 'Perro', 'Labrador', 'Macho'], ['Michi', 'Gato', 'Siames', 'Hembra'],
                ['Rocky', 'Perro', 'Bulldog', 'Macho'], ['Luna', 'Gato', 'Persa', 'Hembra'],
                ['Max', 'Perro', 'Pastor Aleman', 'Macho'], ['Nina', 'Perro', 'Poodle', 'Hembra'],
                ['Simba', 'Gato', 'Angora', 'Macho'], ['Coco', 'Ave', 'Canario', 'Hembra'],
                ['Toby', 'Perro', 'Beagle', 'Macho'], ['Pelusa', 'Conejo', 'Mini Lop', 'Hembra'],
            ];
            $clientes = [];
            foreach ($duenos as $d) {
                $clientes[] = Cliente::create(['empresa_id' => $eid, 'nombre' => $d[0], 'telefono' => $d[1], 'email' => $d[2], 'documento' => (string) random_int(40000000, 79999999)]);
            }
            foreach ($mascotasDemo as $i => $m) {
                Mascota::create(['empresa_id' => $eid, 'cliente_id' => $clientes[$i % count($clientes)]->id, 'nombre' => $m[0], 'especie' => $m[1], 'raza' => $m[2], 'sexo' => $m[3], 'peso' => random_int(2, 35) + 0.5, 'fecha_nacimiento' => Carbon::now()->subMonths(random_int(6, 96))]);
            }
            $productos = [
                ['Alimento Premium Perro 15kg', 'Alimento', 25, 5, 189.90, 120], ['Alimento Gato Adulto 3kg', 'Alimento', 18, 5, 79.90, 45],
                ['Antipulgas Pipeta', 'Farmacia', 40, 10, 35.00, 18], ['Vacuna Antirrabica', 'Farmacia', 30, 8, 45.00, 20],
                ['Shampoo Medicado', 'Higiene', 22, 6, 28.50, 12], ['Desparasitante Oral', 'Farmacia', 50, 10, 15.00, 6],
                ['Collar Antipulgas', 'Accesorios', 15, 5, 42.00, 22], ['Juguete Mordedor', 'Accesorios', 35, 8, 19.90, 8],
                ['Arena Sanitaria 10kg', 'Higiene', 20, 5, 32.00, 16], ['Suplemento Vitaminico', 'Farmacia', 12, 4, 55.00, 30],
            ];
            foreach ($productos as $p) {
                Producto::create(['empresa_id' => $eid, 'nombre' => $p[0], 'categoria' => $p[1], 'stock' => $p[2], 'stock_minimo' => $p[3], 'precio' => $p[4], 'costo' => $p[5], 'sku' => 'SKU-'.random_int(1000, 9999)]);
            }
            $vet = User::where('rol', 'veterinario')->first();
            $mascotas = Mascota::withoutGlobalScope('empresa')->get();
            $motivos = ['Consulta general', 'Vacunacion', 'Control post-operatorio', 'Desparasitacion', 'Peluqueria'];
            foreach (range(1, 6) as $n) {
                Cita::create(['empresa_id' => $eid, 'mascota_id' => $mascotas->random()->id, 'user_id' => $vet?->id, 'fecha' => Carbon::now()->addDays(random_int(0, 6))->setTime(random_int(9, 17), [0, 30][random_int(0, 1)]), 'motivo' => $motivos[array_rand($motivos)], 'estado' => 'pendiente']);
            }
        }

        // ----- Demo clinico y facturacion -----
        if (Comprobante::withoutGlobalScope('empresa')->count() === 0 && Mascota::withoutGlobalScope('empresa')->count() > 0) {
            $vet = User::where('rol', 'veterinario')->first();
            $admin = User::where('email', 'admin@example.com')->first();
            $mascotas = Mascota::withoutGlobalScope('empresa')->with('cliente')->get();
            foreach ($mascotas->take(6) as $m) {
                Consulta::create(['empresa_id' => $eid, 'mascota_id' => $m->id, 'user_id' => $vet?->id, 'fecha' => Carbon::now()->subDays(random_int(1, 40)), 'motivo' => 'Consulta general', 'sintomas' => 'Revision de rutina, sin anomalias.', 'diagnostico' => 'Paciente sano', 'tratamiento' => 'Dieta balanceada y control anual.', 'peso' => $m->peso, 'temperatura' => 38.5]);
                Vacuna::create(['empresa_id' => $eid, 'mascota_id' => $m->id, 'user_id' => $vet?->id, 'nombre' => 'Antirrabica', 'fecha_aplicacion' => Carbon::now()->subMonths(random_int(1, 10)), 'proxima_dosis' => Carbon::now()->addDays(random_int(-10, 40)), 'lote' => 'L'.random_int(1000, 9999)]);
            }
            $productos = Producto::withoutGlobalScope('empresa')->get();
            foreach (range(1, 8) as $n) {
                $prod = $productos->random();
                $cant = random_int(1, 3);
                $subtotal = round($prod->precio * $cant, 2);
                $igv = round($subtotal * 0.18, 2);
                $comp = Comprobante::create(['empresa_id' => $eid, 'cliente_id' => $mascotas->random()->cliente_id, 'user_id' => $admin?->id, 'tipo' => ['ticket', 'boleta'][random_int(0, 1)], 'serie' => 'B001', 'numero' => $n, 'fecha' => Carbon::now()->subDays(random_int(0, 25)), 'subtotal' => $subtotal, 'igv' => $igv, 'total' => round($subtotal + $igv, 2), 'metodo_pago' => 'efectivo', 'estado' => 'emitido']);
                $comp->items()->create(['producto_id' => $prod->id, 'descripcion' => $prod->nombre, 'cantidad' => $cant, 'precio_unitario' => $prod->precio, 'importe' => $subtotal]);
            }
        }

        // Habilitar el portal del cliente para un cliente demo (correo + 'password')
        $demoCliente = Cliente::where('empresa_id', $empresa->id)->orderBy('id')->first();
        if ($demoCliente && ! $demoCliente->acceso_portal) {
            $demoCliente->update(['acceso_portal' => true, 'password' => 'password']);
        }
    }
}
