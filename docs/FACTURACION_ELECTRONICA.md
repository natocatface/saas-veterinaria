# Facturación Electrónica (SUNAT · Perú) — Guía de puesta en marcha

Módulo de emisión de comprobantes electrónicos ante SUNAT (UBL 2.1) integrado en VetSystem.
Cubre facturas, boletas, notas de crédito, resúmenes diarios de boletas (RC) y comunicaciones de baja (RA),
con firma digital y envío directo mediante **Greenter**.

---

## 1. Requisitos

- **PHP 8.2+** con las extensiones `openssl`, `soap`, `mbstring` y `dom` (todas habituales en Laravel).
- **Greenter** (`greenter/lite`) — ya declarado en `composer.json` e instalado en `vendor/`.
- **Certificado digital** en formato `.pem` (ver sección 3).
- *(Opcional)* Extensión **GD** + `endroid/qr-code` para el QR del PDF.

No necesitas instalar nada de Greenter: ya viene con el proyecto.

---

## 2. Migraciones

Ejecuta una sola vez para crear las tablas del módulo:

```bash
php artisan migrate
```

Esto crea/actualiza:

- `facturacion_configs` — configuración por empresa + columnas `sunat_*` en `comprobantes`.
- `notas_credito` — notas de crédito electrónicas.
- `resumenes_diarios` — resúmenes diarios de boletas (RC) + `resumen_id` en `comprobantes`.
- `comunicaciones_baja` — comunicaciones de baja de facturas (RA).

---

## 3. Certificado digital (.pem)

SUNAT exige firmar cada comprobante con un certificado digital.

**Ruta por defecto:** `storage/app/facturacion/pe/certificate.pem`
(puedes cambiarla en el formulario del módulo).

- **Entorno Beta (homologación):** usa el certificado de pruebas de SUNAT/Greenter. Con él puedes
  emitir con el RUC de pruebas `20000000001` y usuario/clave SOL `MODDATOS`.
- **Producción:** usa tu certificado real. Si lo tienes en `.pfx`/`.p12`, conviértelo a `.pem`:

```bash
openssl pkcs12 -in tu_certificado.pfx -out certificate.pem -nodes
```

Coloca el archivo resultante en la ruta indicada. El módulo muestra en verde/rojo si lo encuentra.

---

## 4. Configuración en la aplicación

Menú lateral → **Facturación Electrónica** (visible para administradores).

1. **Datos del emisor:** RUC, razón social, dirección fiscal, ubigeo, departamento/provincia/distrito.
2. **Credenciales SUNAT:** usuario y clave SOL, ruta del certificado.
3. **Series:** boleta (`B001`), factura (`F001`), nota de crédito (`FC01`).
4. **Estado y modo:**
   - *Driver de emisión:* selecciona **Greenter (firma y envía a SUNAT)**.
   - *Entorno:* **Beta** para pruebas, **Producción** cuando estés listo.
   - Marca **Habilitar facturación electrónica** y, si quieres emitir al cerrar la venta,
     **Emitir automáticamente al cerrar la venta**.
5. Pulsa **Guardar configuración** y luego **Probar conexión con SUNAT** para validar que todo esté completo.

---

## 5. Prueba en Beta (homologación)

1. Driver **Greenter**, entorno **Beta**, RUC `20000000001`, usuario/clave `MODDATOS`, certificado de pruebas.
2. Registra una **boleta** o **factura** desde el módulo de Facturación.
3. Al guardarse (con emisión automática activa) se envía a SUNAT. Abre el comprobante para ver el
   **estado SUNAT** (Aceptado/Rechazado), el mensaje y el hash.
4. Descarga el **XML firmado** y el **CDR** desde la ficha del comprobante.

> Para facturas de prueba, el cliente debe tener un RUC válido (11 dígitos).

---

## 6. Flujos disponibles

| Acción | Dónde | Documento SUNAT |
|---|---|---|
| Emitir factura/boleta | Al registrar el comprobante (auto o botón "Emitir a SUNAT") | 01 / 03 |
| Reenviar a SUNAT | Ficha del comprobante → "Reenviar a SUNAT" | — |
| Descargar XML / CDR / PDF | Ficha del comprobante | — |
| Nota de crédito | Ficha del comprobante (aceptado) → motivo + "Emitir nota de crédito" | 07 |
| Resumen diario de boletas | Reportes → Facturación → "Resúmenes de boletas" | RC |
| Comunicación de baja (factura) | Ficha de la factura (aceptada) → "Comunicar baja" | RA |
| Consultar estado por ticket | En cada resumen/baja → "Consultar estado" | — |

El **PDF con QR** está disponible en cada comprobante (botón "PDF"). El QR sigue la especificación de SUNAT.

---

## 7. Sincronización automática (tickets RC/RA)

Los resúmenes (RC) y bajas (RA) son **asíncronos**: SUNAT devuelve un *ticket* que hay que consultar luego.
El comando lo hace automáticamente para todas las empresas con Greenter habilitado:

```bash
php artisan facturacion:sync-tickets
```

Ya está programado para ejecutarse **cada 15 minutos**. Para que el scheduler de Laravel funcione,
agrega un disparador del sistema que ejecute cada minuto:

**Windows (Programador de tareas):** crea una tarea básica que se repita cada 1 minuto y ejecute:

```
php C:\SAAS\saas-veterinaria\artisan schedule:run
```

**Linux (cron):**

```
* * * * * cd /ruta/al/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

---

## 8. Paso a producción

1. Reemplaza el certificado de pruebas por tu **certificado real** en `.pem`.
2. En el módulo: RUC y razón social **reales**, usuario/clave SOL de producción.
3. Cambia el **entorno** a **Producción** y ajusta las **series** autorizadas.
4. Emite un comprobante real de prueba y verifica el CDR **Aceptado**.

> Recomendación: mantén Beta hasta completar la homologación de SUNAT para los tipos de documento que uses.

---

## 9. Código QR del PDF (opcional)

El PDF se genera siempre; el QR aparece si hay una librería instalada. Para el QR real:

```bash
composer require endroid/qr-code:^4.8
```

Requiere la extensión **GD**. Si prefieres Imagick: `composer require simplesoftwareio/simple-qrcode`.
Sin ninguna, el PDF muestra el texto del QR como respaldo.

---

## 10. Solución de problemas

- **"Certificado no encontrado":** revisa la ruta en el módulo y que el `.pem` exista y sea legible.
- **Rechazado por SUNAT (2xxx):** revisa el mensaje en la ficha; suele ser RUC/serie/monto inválido.
- **"En proceso (código 98)":** el resumen/baja aún se está procesando; vuelve a consultar en unos minutos
  (o espera al comando programado).
- **Las notas de crédito requieren driver Greenter:** cambia el driver en la configuración.
- **Nada se envía:** confirma que la facturación esté **habilitada** y el driver sea **Greenter**.

---

## 11. Archivos generados

Los documentos firmados y las respuestas de SUNAT se guardan en:

```
storage/app/facturacion/pe/
├── xml/       # comprobantes y notas firmados
├── cdr/       # constancias de recepción (CDR) de SUNAT
├── resumen/   # XML de resúmenes diarios (RC)
└── baja/      # XML de comunicaciones de baja (RA)
```

---

## 12. Referencia técnica (arquitectura)

- **Configuración:** `App\Models\FacturacionConfig` (singleton por empresa).
- **Orquestador:** `App\Services\Facturacion\FacturacionManager` (resuelve el driver y aplica efectos).
- **Drivers:** `Ninguno` (no emite), `Beta` (genera XML sin enviar), `Greenter` (firma y envía).
  Contrato: `App\Services\Facturacion\Contracts\FacturacionDriver`.
- **Documentos SUNAT** (vía Greenter): factura/boleta (`Invoice`), nota de crédito (`Note`),
  resumen (`Summary`), baja (`Voided`).
- **Utilidades:** `UblXmlBuilder`, `NumeroALetras`, `QrGenerator`.
- **Automatización:** comando `facturacion:sync-tickets` (scheduler cada 15 min).

Para cambiar de proveedor de emisión (por ejemplo, otro OSE), basta con implementar
`FacturacionDriver` y registrarlo en `FacturacionManager::driver()`.
