<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"></head>
<body style="margin:0;background:#f1f5f9;font-family:Arial,Helvetica,sans-serif;color:#334155;">
    <div style="max-width:560px;margin:0 auto;padding:24px;">
        <div style="background:#0891b2;color:#fff;border-radius:16px 16px 0 0;padding:24px;">
            <h1 style="margin:0;font-size:20px;">VetSystem</h1>
            <p style="margin:4px 0 0;opacity:.85;font-size:13px;">Recordatorio de cita</p>
        </div>
        <div style="background:#fff;border-radius:0 0 16px 16px;padding:24px;">
            <p style="font-size:15px;">Hola{{ optional(optional($cita->mascota)->cliente)->nombre ? ' '.optional($cita->mascota->cliente)->nombre : '' }},</p>
            <p style="font-size:15px;">Te recordamos la cita de <strong>{{ optional($cita->mascota)->nombre }}</strong>:</p>
            <table style="width:100%;font-size:14px;border-collapse:collapse;margin:16px 0;">
                <tr><td style="padding:8px 0;color:#94a3b8;">Fecha</td><td style="padding:8px 0;font-weight:bold;text-align:right;">{{ $cita->fecha->format('d/m/Y') }}</td></tr>
                <tr><td style="padding:8px 0;color:#94a3b8;">Hora</td><td style="padding:8px 0;font-weight:bold;text-align:right;">{{ $cita->fecha->format('H:i') }}</td></tr>
                <tr><td style="padding:8px 0;color:#94a3b8;">Motivo</td><td style="padding:8px 0;font-weight:bold;text-align:right;">{{ $cita->motivo ?: 'Consulta general' }}</td></tr>
                <tr><td style="padding:8px 0;color:#94a3b8;">Veterinario</td><td style="padding:8px 0;font-weight:bold;text-align:right;">{{ optional($cita->veterinario)->name ?: 'Por asignar' }}</td></tr>
            </table>
            <p style="font-size:13px;color:#94a3b8;">Si no puedes asistir, por favor comunicate con la clinica para reprogramar.</p>
        </div>
        <p style="text-align:center;font-size:11px;color:#94a3b8;margin-top:16px;">Este es un mensaje automatico de VetSystem.</p>
    </div>
</body>
</html>
