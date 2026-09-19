<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EmpresaController;
use App\Http\Controllers\Admin\PagoController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\UsuarioController as AdminUsuarioController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegistroController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ComprobanteController;
use App\Http\Controllers\ComunicacionBajaController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FacturacionElectronicaController;
use App\Http\Controllers\GroomingController;
use App\Http\Controllers\HistoriaController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\MascotaController;
use App\Http\Controllers\NotaCreditoController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\Portal\PortalAuthController;
use App\Http\Controllers\Portal\PortalController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ResumenDiarioController;
use App\Http\Controllers\SuscripcionController;
use App\Http\Controllers\TeleconsultaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VacunaController;
use Illuminate\Support\Facades\Route;

// ----- Landing publica -----
Route::get('/', [LandingController::class, 'index'])->name('home');

// ----- Autenticacion y registro publico -----
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/registro', [RegistroController::class, 'show'])->name('registro.show');
    Route::post('/registro', [RegistroController::class, 'store'])->name('registro.store');

    // Recuperacion de contrasena
    Route::get('/forgot-password', [PasswordResetController::class, 'showForgot'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showReset'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ===================== PORTAL DEL CLIENTE (dueños de mascotas) =====================
Route::prefix('portal')->group(function () {
    Route::middleware('guest:cliente')->group(function () {
        Route::get('login', [PortalAuthController::class, 'showLogin'])->name('portal.login');
        Route::post('login', [PortalAuthController::class, 'login'])->name('portal.login.attempt');
    });
    Route::middleware('auth:cliente')->group(function () {
        Route::post('logout', [PortalAuthController::class, 'logout'])->name('portal.logout');
        Route::get('/', [PortalController::class, 'dashboard'])->name('portal.dashboard');
        Route::get('mascotas', [PortalController::class, 'mascotas'])->name('portal.mascotas');
        Route::get('citas', [PortalController::class, 'citas'])->name('portal.citas');
        Route::get('vacunas', [PortalController::class, 'vacunas'])->name('portal.vacunas');
    });
});

// ----- Zona autenticada comun (perfil / suscripcion) -----
Route::middleware('auth')->group(function () {
    Route::get('/perfil', [PerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');
    Route::put('/perfil/password', [PerfilController::class, 'updatePassword'])->name('perfil.password');
    Route::get('/suscripcion/bloqueado', [SuscripcionController::class, 'bloqueado'])->name('suscripcion.bloqueado');
});

// ===================== PANEL SUPER ADMIN (plataforma SaaS) =====================
Route::middleware(['auth', 'super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('empresas', EmpresaController::class);
    Route::patch('empresas/{empresa}/estado/{estado}', [EmpresaController::class, 'cambiarEstado'])->name('empresas.estado');
    Route::get('empresas/{empresa}/pago', [PagoController::class, 'create'])->name('empresas.pago.create');
    Route::post('empresas/{empresa}/pago', [PagoController::class, 'store'])->name('empresas.pago.store');
    Route::resource('planes', PlanController::class)->except(['show']);
    Route::get('suscripciones', [PagoController::class, 'index'])->name('suscripciones.index');
    Route::get('usuarios', [AdminUsuarioController::class, 'index'])->name('usuarios.index');
});

// ===================== AREA DE CLINICA (tenant) =====================
Route::middleware(['auth', 'tenant'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Gestion
    Route::resource('clientes', ClienteController::class);
    Route::patch('clientes/{cliente}/estado', [ClienteController::class, 'toggleEstado'])->name('clientes.estado');
    Route::resource('mascotas', MascotaController::class);
    Route::patch('mascotas/{mascota}/estado', [MascotaController::class, 'toggleEstado'])->name('mascotas.estado');
    Route::resource('citas', CitaController::class)->except(['show']);
    Route::patch('citas/{cita}/estado/{estado}', [CitaController::class, 'cambiarEstado'])->name('citas.cambiar-estado');

    // Clinica
    Route::resource('historia', HistoriaController::class)->parameters(['historia' => 'historia']);
    Route::resource('vacunas', VacunaController::class)->except(['show']);
    Route::resource('telemedicina', TeleconsultaController::class)->parameters(['telemedicina' => 'telemedicina'])->except(['show']);
    Route::patch('telemedicina/{telemedicina}/estado/{estado}', [TeleconsultaController::class, 'cambiarEstado'])->name('telemedicina.cambiar-estado');
    Route::resource('peluqueria', GroomingController::class)->parameters(['peluqueria' => 'peluqueria'])->except(['show']);
    Route::patch('peluqueria/{peluqueria}/estado/{estado}', [GroomingController::class, 'cambiarEstado'])->name('peluqueria.cambiar-estado');

    // Comercial
    Route::resource('productos', ProductoController::class)->except(['show']);
    Route::patch('productos/{producto}/stock', [ProductoController::class, 'ajustarStock'])->name('productos.stock');
    Route::resource('comprobantes', ComprobanteController::class)->except(['edit', 'update']);
    Route::patch('comprobantes/{comprobante}/anular', [ComprobanteController::class, 'anular'])->name('comprobantes.anular');
    Route::post('comprobantes/{comprobante}/sunat', [ComprobanteController::class, 'emitirSunat'])->name('comprobantes.sunat');
    Route::get('comprobantes/{comprobante}/xml', [ComprobanteController::class, 'descargarXml'])->name('comprobantes.xml');
    Route::get('comprobantes/{comprobante}/cdr', [ComprobanteController::class, 'descargarCdr'])->name('comprobantes.cdr');
    Route::get('comprobantes/{comprobante}/pdf', [ComprobanteController::class, 'pdf'])->name('comprobantes.pdf');
    Route::post('comprobantes/{comprobante}/nota-credito', [NotaCreditoController::class, 'store'])->name('notas.store');
    Route::get('notas-credito/{nota}/xml', [NotaCreditoController::class, 'descargarXml'])->name('notas.xml');
    Route::get('notas-credito/{nota}/cdr', [NotaCreditoController::class, 'descargarCdr'])->name('notas.cdr');

    // Resumenes diarios de boletas (RC) + consulta por ticket
    Route::get('resumenes-diarios', [ResumenDiarioController::class, 'index'])->name('resumenes.index');
    Route::post('resumenes-diarios', [ResumenDiarioController::class, 'generar'])->name('resumenes.generar');
    Route::post('resumenes-diarios/{resumen}/consultar', [ResumenDiarioController::class, 'consultar'])->name('resumenes.consultar');
    Route::get('resumenes-diarios/{resumen}/xml', [ResumenDiarioController::class, 'descargarXml'])->name('resumenes.xml');
    Route::get('resumenes-diarios/{resumen}/cdr', [ResumenDiarioController::class, 'descargarCdr'])->name('resumenes.cdr');

    // Comunicaciones de baja (RA) de facturas
    Route::post('comprobantes/{comprobante}/baja', [ComunicacionBajaController::class, 'store'])->name('baja.store');
    Route::post('comunicaciones-baja/{baja}/consultar', [ComunicacionBajaController::class, 'consultar'])->name('baja.consultar');
    Route::get('comunicaciones-baja/{baja}/xml', [ComunicacionBajaController::class, 'descargarXml'])->name('baja.xml');
    Route::get('comunicaciones-baja/{baja}/cdr', [ComunicacionBajaController::class, 'descargarCdr'])->name('baja.cdr');
    Route::get('caja', [CajaController::class, 'index'])->name('caja.index');
    Route::post('caja/abrir', [CajaController::class, 'abrir'])->name('caja.abrir');
    Route::patch('caja/{caja}/movimiento', [CajaController::class, 'movimiento'])->name('caja.movimiento');
    Route::patch('caja/{caja}/cerrar', [CajaController::class, 'cerrar'])->name('caja.cerrar');

    // Gerencia
    Route::get('reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('reportes/facturacion-electronica', [ReporteController::class, 'facturacionElectronica'])->name('reportes.facturacion');

    // Exportaciones (Excel/CSV)
    Route::get('exportar/clientes', [ExportController::class, 'clientes'])->name('export.clientes');
    Route::get('exportar/pacientes', [ExportController::class, 'mascotas'])->name('export.mascotas');
    Route::get('exportar/citas', [ExportController::class, 'citas'])->name('export.citas');
    Route::get('exportar/inventario', [ExportController::class, 'productos'])->name('export.productos');
    Route::get('exportar/comprobantes', [ExportController::class, 'comprobantes'])->name('export.comprobantes');

    // Sistema (solo admin de la clinica)
    Route::middleware('admin')->group(function () {
        Route::resource('usuarios', UserController::class)->except(['show']);
        Route::patch('usuarios/{usuario}/estado', [UserController::class, 'toggleEstado'])->name('usuarios.estado');
        Route::get('configuracion', [ConfiguracionController::class, 'edit'])->name('configuracion.edit');
        Route::put('configuracion', [ConfiguracionController::class, 'update'])->name('configuracion.update');

        // Facturacion Electronica (SUNAT)
        Route::get('facturacion/configuracion', [FacturacionElectronicaController::class, 'edit'])->name('facturacion.config.edit');
        Route::put('facturacion/configuracion', [FacturacionElectronicaController::class, 'update'])->name('facturacion.config.update');
        Route::post('facturacion/configuracion/probar', [FacturacionElectronicaController::class, 'probar'])->name('facturacion.config.probar');

        // Mi suscripcion (autoservicio de plan)
        Route::get('suscripcion', [SuscripcionController::class, 'index'])->name('suscripcion.index');
        Route::put('suscripcion/plan', [SuscripcionController::class, 'cambiarPlan'])->name('suscripcion.plan');

        Route::get('auditoria', [AuditoriaController::class, 'index'])->name('auditoria.index');
    });
});
