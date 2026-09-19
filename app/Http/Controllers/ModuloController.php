<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModuloController extends Controller
{
    // Catalogo de modulos aun en construccion (fase siguiente del proyecto)
    private array $modulos = [
        'clientes'   => ['titulo' => 'Clientes',        'icono' => 'users',     'desc' => 'Registro y gestion de duenos de mascotas, historial de contacto y estado de cuenta.'],
        'agenda'     => ['titulo' => 'Agenda',          'icono' => 'calendar',  'desc' => 'Programacion de citas, calendario por veterinario y recordatorios automaticos.'],
        'historia'   => ['titulo' => 'Historia Clinica','icono' => 'clipboard', 'desc' => 'Fichas clinicas por mascota, diagnosticos, tratamientos y evolucion.'],
        'telemedicina' => ['titulo' => 'Telemedicina',  'icono' => 'video',     'desc' => 'Consultas veterinarias en linea por videollamada y seguimiento remoto.'],
        'vacunaciones' => ['titulo' => 'Vacunaciones',  'icono' => 'shield',    'desc' => 'Calendario de vacunas, control sanitario y certificados.'],
        'peluqueria' => ['titulo' => 'Peluqueria',      'icono' => 'scissors',  'desc' => 'Servicios de grooming, bano y estetica canina/felina.'],
        'inventario' => ['titulo' => 'Inventario',      'icono' => 'box',       'desc' => 'Control de stock de alimentos, farmacia y accesorios con alertas.'],
        'facturacion' => ['titulo' => 'Facturacion',    'icono' => 'document',  'desc' => 'Emision de comprobantes, boletas y facturas electronicas.'],
        'caja'       => ['titulo' => 'Control de Caja',  'icono' => 'wallet',    'desc' => 'Apertura y cierre de caja, ingresos, egresos y arqueo diario.'],
        'reportes'   => ['titulo' => 'Reportes y BI',   'icono' => 'chart',     'desc' => 'Indicadores del negocio, ingresos, servicios mas solicitados y tendencias.'],
        'usuarios'   => ['titulo' => 'Usuarios',        'icono' => 'cog',       'desc' => 'Gestion de personal, roles y permisos del sistema.'],
        'configuracion' => ['titulo' => 'Configuracion','icono' => 'sliders',   'desc' => 'Datos de la clinica, parametros generales y preferencias.'],
    ];

    public function show(string $modulo)
    {
        $data = $this->modulos[$modulo] ?? ['titulo' => ucfirst($modulo), 'icono' => 'box', 'desc' => 'Modulo del sistema.'];

        return view('modulos.placeholder', [
            'modulo' => $modulo,
            'titulo' => $data['titulo'],
            'icono' => $data['icono'],
            'desc' => $data['desc'],
        ]);
    }
}
