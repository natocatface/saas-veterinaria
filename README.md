# VetSystem — SaaS Veterinaria

Sistema de gestión para clínicas veterinarias construido con **Laravel 11** y **MySQL**.
Esta primera entrega incluye: base del proyecto, **login funcional**, **dashboard** con métricas y gráficos, y el **menú vertical con todos los módulos** del sistema (los módulos operativos se implementan en fases siguientes).

## Requisitos

- PHP 8.2 o superior
- Composer 2
- MySQL 5.7+ / MariaDB (servidor local, XAMPP/Laragon sirven)
- Extensiones PHP: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`

## Instalación (local)

Desde la carpeta del proyecto (`saas-veterinaria`):

```bash
# 1. Instalar dependencias PHP
composer install

# 2. El archivo .env ya viene configurado. Genera la clave de la app:
php artisan key:generate

# 3. Crear la base de datos (una sola vez).
#    Opción A: importar el archivo incluido
mysql -u root -e "SOURCE database/crear_base_datos.sql"
#    Opción B: crearla manualmente en phpMyAdmin/HeidiSQL con el nombre:  saas_veterinaria

# 4. Ejecutar migraciones + datos de ejemplo (admin, mascotas, inventario, citas)
php artisan migrate --seed

# 5. Levantar el servidor
php artisan serve
```

Abre: **http://127.0.0.1:8000**

## Configuración MySQL

Definida en `.env` (ajusta la contraseña si tu MySQL tiene una):

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=saas_veterinaria
DB_USERNAME=root
DB_PASSWORD=
```

## Credenciales de acceso

| Rol                    | Correo                  | Contraseña |
|------------------------|-------------------------|------------|
| **Super Admin (SaaS)** | superadmin@example.com  | password   |
| Administrador (clínica)| admin@example.com       | password   |
| Veterinario            | vet@example.com         | password   |
| Recepción              | recepcion@example.com   | password   |

> El **Super Admin** entra al panel de plataforma (`/admin`): gestiona empresas/clínicas, planes de suscripción, métricas globales (MRR) y todos los usuarios. Los demás usuarios entran al panel de su clínica y solo ven los datos de su empresa (multi-tenancy con `empresa_id`).


### Funcionalidades SaaS
- **Registro público de clínicas** en `/registro`: una clínica se registra sola, elige plan y arranca con **15 días de prueba** (crea su empresa + usuario admin y entra automáticamente).
- **Vencimiento de suscripción**: aviso en el panel cuando faltan ≤7 días, **suspensión automática** al vencer (comando `php artisan suscripciones:verificar`, programado a diario) y pantalla de *suscripción suspendida*.
- **Perfil de usuario** en `/perfil`: cualquier usuario edita sus datos y **cambia su contraseña**.
- **Facturación SaaS** (super admin): registrar pagos por empresa (renueva la vigencia), e historial global en `/admin/suscripciones`.
- **Recuperación de contraseña**: flujo "olvidé mi contraseña" en `/forgot-password` (en local el enlace sale en `storage/logs/laravel.log`).
- **Portal del cliente** en `/portal/login`: los dueños ven sus mascotas, citas y vacunas. Se habilita por cliente desde el módulo Clientes (checkbox "Acceso al portal" + contraseña). Demo: el primer cliente queda con acceso y contraseña `password`.
- **Auditoría / bitácora** en el menú (solo admin): registra automáticamente quién creó/editó/eliminó cada registro.
- **Recordatorios de citas por correo**: comando `php artisan citas:recordatorio` (programado a las 08:00) que envía recordatorio de las citas del día siguiente.

Para cargar datos demo distribuidos en fechas (poblar dashboard y gráficos):
```bash
php artisan db:seed --class=DemoSeeder
```

## Actualizar la base de datos (si ya la tenías creada)

Si ya habías corrido el proyecto antes, aplica las nuevas tablas y carga los datos demo del bloque clínico/comercial:

```bash
php artisan migrate      # crea planes, empresas, suscripcion_pagos, auditorias, portal de clientes, etc.
php artisan db:seed      # carga configuración de la clínica + demo de consultas, vacunas y ventas
```

## Módulos del sistema

El sistema está **completo**, con todos los módulos operativos:

- **Autenticación** con roles (admin, veterinario, recepción, groomer)
- **Dashboard** con métricas e ingresos reales
- **Clientes** y **Pacientes/Mascotas** (CRUD con fichas y relaciones)
- **Agenda de Citas** con estados y asignación a veterinario
- **Historia Clínica** (consultas, diagnóstico, tratamiento, evolución, ficha imprimible)
- **Telemedicina** (teleconsultas con enlace de videollamada y estados)
- **Vacunaciones** (calendario, próximas dosis y alertas de vencidas)
- **Peluquería** (servicios de grooming con estados e ingresos)
- **Inventario** (stock, alertas de stock bajo, ajuste rápido)
- **Facturación** (ticket/boleta/factura con IGV, impresión, anulación con reposición de stock)
- **Control de Caja** (apertura, ingresos/egresos, cierre y arqueo; las ventas registran ingreso automático)
- **Reportes y BI** (KPIs, ingresos por mes, top productos y clientes, gráficos)
- **Usuarios** (gestión de personal y roles, solo admin) y **Configuración** de la clínica

## Stack técnico

- Laravel 11 (PHP 8.2)
- MySQL
- Blade + Tailwind CSS (CDN) + Alpine.js
- Chart.js para gráficos
- Sin paso de build (npm): todo el front carga por CDN para facilitar el despliegue local.

## Estructura relevante

```
app/Http/Controllers/Auth/AuthController.php   Login / logout
app/Http/Controllers/DashboardController.php   Métricas del panel
app/Http/Controllers/ModuloController.php      Pantallas de módulos
app/Models/                                    User, Cliente, Mascota, Cita, Producto
database/migrations/                           Esquema de la BD
database/seeders/DatabaseSeeder.php            Usuarios y datos demo
resources/views/auth/login.blade.php           Pantalla de login
resources/views/layouts/app.blade.php          Layout + menú vertical
resources/views/dashboard/index.blade.php      Dashboard
resources/views/modulos/placeholder.blade.php  Módulos en construcción
routes/web.php                                 Rutas
```
