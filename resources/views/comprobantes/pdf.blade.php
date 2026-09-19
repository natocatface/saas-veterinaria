<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ $comprobante->numeroFormateado() }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; }
        .wrap { padding: 6px; }
        table { width: 100%; border-collapse: collapse; }
        .head td { vertical-align: top; }
        .emisor h1 { font-size: 16px; color: #0e7490; margin-bottom: 3px; }
        .emisor p { font-size: 10px; color: #475569; line-height: 1.4; }
        .doc-box {
            border: 2px solid #0e7490; border-radius: 8px; text-align: center;
            padding: 10px 8px; width: 210px;
        }
        .doc-box .ruc { font-size: 12px; font-weight: bold; color: #0e7490; }
        .doc-box .tipo { font-size: 12px; font-weight: bold; margin: 4px 0; text-transform: uppercase; }
        .doc-box .num { font-size: 15px; font-weight: bold; color: #0f172a; }
        .meta { margin-top: 14px; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 10px; }
        .meta td { padding: 2px 0; font-size: 10px; }
        .meta .lbl { color: #64748b; font-weight: bold; width: 90px; }
        .items { margin-top: 10px; }
        .items th {
            background: #0e7490; color: #fff; font-size: 10px; padding: 6px 8px; text-align: left;
        }
        .items th.r, .items td.r { text-align: right; }
        .items th.c, .items td.c { text-align: center; }
        .items td { padding: 6px 8px; border-bottom: 1px solid #eef2f6; font-size: 10px; }
        .totales { margin-top: 8px; }
        .totales td { padding: 3px 8px; font-size: 11px; }
        .totales .lbl { text-align: right; color: #475569; }
        .totales .val { text-align: right; width: 110px; }
        .totales .grand { font-size: 14px; font-weight: bold; color: #0f172a; border-top: 2px solid #0e7490; }
        .letras { margin-top: 8px; font-size: 10px; color: #334155; font-style: italic; }
        .foot { margin-top: 16px; }
        .qr-cell { width: 160px; text-align: center; vertical-align: top; }
        .qr-cell img { width: 140px; height: 140px; }
        .qr-fallback {
            font-family: DejaVu Sans Mono, monospace; font-size: 7px; word-break: break-all;
            border: 1px dashed #94a3b8; padding: 6px; color: #475569; text-align: left;
        }
        .leyenda { font-size: 9px; color: #64748b; vertical-align: bottom; line-height: 1.5; }
        .estado-sunat { font-size: 9px; font-weight: bold; padding: 2px 6px; border-radius: 4px; }
        .ok { background: #dcfce7; color: #15803d; }
        .rej { background: #fee2e2; color: #b91c1c; }
        .pen { background: #fef9c3; color: #a16207; }
    </style>
</head>
<body>
<div class="wrap">
    <table class="head">
        <tr>
            <td class="emisor">
                <h1>{{ $fe->razon_social ?: $config->nombre_clinica }}</h1>
                @if ($fe->nombre_comercial)<p>{{ $fe->nombre_comercial }}</p>@endif
                <p>{{ $fe->direccion_fiscal ?: $config->direccion }}</p>
                <p>{{ trim(($fe->distrito ? $fe->distrito.' - ' : '').($fe->provincia ? $fe->provincia.' - ' : '').$fe->departamento, ' -') }}</p>
                @if ($fe->sol_usuario)<p>Correo: {{ $config->email }}</p>@endif
            </td>
            <td style="text-align: right;">
                <div class="doc-box" style="display: inline-block;">
                    <div class="ruc">R.U.C. {{ $fe->ruc }}</div>
                    <div class="tipo">{{ $comprobante->tipo === 'factura' ? 'Factura Electronica' : ($comprobante->tipo === 'boleta' ? 'Boleta de Venta Electronica' : 'Comprobante Interno') }}</div>
                    <div class="num">{{ $comprobante->numeroFormateado() }}</div>
                </div>
            </td>
        </tr>
    </table>

    <table class="meta">
        <tr>
            <td class="lbl">Cliente:</td>
            <td>{{ optional($comprobante->cliente)->nombre ?: 'Cliente varios' }}</td>
            <td class="lbl">Fecha emision:</td>
            <td>{{ $comprobante->fecha->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="lbl">Documento:</td>
            <td>{{ optional($comprobante->cliente)->documento ?: '-' }}</td>
            <td class="lbl">Hora:</td>
            <td>{{ $comprobante->fecha->format('H:i:s') }}</td>
        </tr>
        <tr>
            <td class="lbl">Moneda:</td>
            <td>Soles (PEN)</td>
            <td class="lbl">Forma de pago:</td>
            <td>{{ ucfirst($comprobante->metodo_pago) }}</td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th class="c">Cant.</th>
                <th>Descripcion</th>
                <th class="r">V. Unit.</th>
                <th class="r">Importe</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($comprobante->items as $item)
                <tr>
                    <td class="c">{{ rtrim(rtrim(number_format($item->cantidad, 2), '0'), '.') }}</td>
                    <td>{{ $item->descripcion }}</td>
                    <td class="r">{{ number_format($item->precio_unitario, 2) }}</td>
                    <td class="r">{{ number_format($item->importe, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totales">
        <tr><td class="lbl">Op. Gravada:</td><td class="val">S/ {{ number_format($comprobante->subtotal, 2) }}</td></tr>
        <tr><td class="lbl">IGV (18%):</td><td class="val">S/ {{ number_format($comprobante->igv, 2) }}</td></tr>
        <tr><td class="lbl grand">Importe Total:</td><td class="val grand">S/ {{ number_format($comprobante->total, 2) }}</td></tr>
    </table>

    <p class="letras">SON: {{ $enLetras }}</p>

    <table class="foot">
        <tr>
            <td class="qr-cell">
                @if ($qrImg)
                    <img src="{{ $qrImg }}" alt="QR">
                @else
                    <div class="qr-fallback">{{ $qrTexto }}</div>
                @endif
            </td>
            <td class="leyenda">
                <p>Representacion impresa del
                    {{ $comprobante->tipo === 'factura' ? 'Comprobante de Pago Electronico (Factura)' : 'Comprobante de Pago Electronico (Boleta)' }}.</p>
                <p>Autorizado mediante Resolucion de SUNAT. Consulte su documento en www.sunat.gob.pe</p>
                @if ($comprobante->sunat_hash)<p>Codigo Hash: {{ $comprobante->sunat_hash }}</p>@endif
                @php
                    $se = $comprobante->sunat_estado;
                    $cls = $se === 'aceptado' ? 'ok' : ($se === 'rechazado' ? 'rej' : 'pen');
                @endphp
                @if ($se)
                    <p style="margin-top: 4px;">Estado SUNAT:
                        <span class="estado-sunat {{ $cls }}">{{ strtoupper($se) }}</span>
                    </p>
                @endif
            </td>
        </tr>
    </table>
</div>
</body>
</html>
