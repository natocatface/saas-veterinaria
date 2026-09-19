<?php

namespace App\Services\Facturacion;

use App\Models\Comprobante;
use App\Models\FacturacionConfig;
use Illuminate\Support\Carbon;

/**
 * Genera el XML UBL 2.1 (SUNAT) de una Boleta (03) o Factura (01).
 *
 * NOTA: Este XML NO esta firmado digitalmente. La firma (XMLDSig con el
 * certificado .pem) y el envio real a los web services de SUNAT se realizan
 * con la libreria Greenter en el driver de produccion. Este builder deja el
 * documento listo (estructura, montos e IGV) para ese paso.
 */
class UblXmlBuilder
{
    public function __construct(private FacturacionConfig $config)
    {
    }

    /** Codigo SUNAT del tipo de comprobante. */
    public function tipoDocumento(Comprobante $c): string
    {
        return match ($c->tipo) {
            'factura' => '01',
            'boleta' => '03',
            default => '03',
        };
    }

    public function build(Comprobante $comprobante): string
    {
        $comprobante->loadMissing('items', 'cliente');

        $cfg = $this->config;
        $tipoDoc = $this->tipoDocumento($comprobante);
        $id = $comprobante->serie.'-'.str_pad((string) $comprobante->numero, 6, '0', STR_PAD_LEFT);
        $fecha = ($comprobante->fecha instanceof Carbon ? $comprobante->fecha : Carbon::parse($comprobante->fecha));
        $moneda = 'PEN';

        // Los precios se guardan SIN IGV (valor neto). Acumulamos por linea
        // para que los totales del comprobante cuadren exactamente.
        $subtotal = 0.0;
        $igv = 0.0;

        // Cliente: para factura se asume RUC (6), para boleta DNI (1) u "sin documento" (0)
        $cliente = $comprobante->cliente;
        $clienteDoc = $cliente->documento ?? '00000000';
        $clienteTipoDoc = $comprobante->tipo === 'factura' ? '6' : (strlen((string) $clienteDoc) === 11 ? '6' : '1');
        $clienteNombre = $cliente->nombre ?? 'CLIENTE VARIOS';

        $e = fn ($v) => htmlspecialchars((string) $v, ENT_XML1 | ENT_QUOTES, 'UTF-8');

        $lineas = '';
        $i = 0;
        foreach ($comprobante->items as $item) {
            $i++;
            $cant = (float) $item->cantidad;
            $valorUnit = round((float) $item->precio_unitario, 6); // valor unitario sin IGV
            $valorVenta = round($valorUnit * $cant, 2);
            $igvLinea = round($valorVenta * 0.18, 2);
            $precioUnitConIgv = round($valorUnit * 1.18, 2);
            $subtotal += $valorVenta;
            $igv += $igvLinea;

            $lineas .= <<<XML

    <cac:InvoiceLine>
      <cbc:ID>{$i}</cbc:ID>
      <cbc:InvoicedQuantity unitCode="NIU">{$cant}</cbc:InvoicedQuantity>
      <cbc:LineExtensionAmount currencyID="{$moneda}">{$valorVenta}</cbc:LineExtensionAmount>
      <cac:PricingReference>
        <cac:AlternativeConditionPrice>
          <cbc:PriceAmount currencyID="{$moneda}">{$precioUnitConIgv}</cbc:PriceAmount>
          <cbc:PriceTypeCode>01</cbc:PriceTypeCode>
        </cac:AlternativeConditionPrice>
      </cac:PricingReference>
      <cac:TaxTotal>
        <cbc:TaxAmount currencyID="{$moneda}">{$igvLinea}</cbc:TaxAmount>
        <cac:TaxSubtotal>
          <cbc:TaxableAmount currencyID="{$moneda}">{$valorVenta}</cbc:TaxableAmount>
          <cbc:TaxAmount currencyID="{$moneda}">{$igvLinea}</cbc:TaxAmount>
          <cac:TaxCategory>
            <cbc:Percent>18.00</cbc:Percent>
            <cbc:TaxExemptionReasonCode>10</cbc:TaxExemptionReasonCode>
            <cac:TaxScheme>
              <cbc:ID>1000</cbc:ID>
              <cbc:Name>IGV</cbc:Name>
              <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
            </cac:TaxScheme>
          </cac:TaxCategory>
        </cac:TaxSubtotal>
      </cac:TaxTotal>
      <cac:Item>
        <cbc:Description>{$e($item->descripcion)}</cbc:Description>
      </cac:Item>
      <cac:Price>
        <cbc:PriceAmount currencyID="{$moneda}">{$valorUnit}</cbc:PriceAmount>
      </cac:Price>
    </cac:InvoiceLine>
    XML;
        }

        $subtotal = round($subtotal, 2);
        $igv = round($igv, 2);
        $total = round($subtotal + $igv, 2);

        $fechaEmision = $fecha->format('Y-m-d');
        $horaEmision = $fecha->format('H:i:s');

        $ruc = $e($cfg->ruc);
        $razon = $e($cfg->razon_social);
        $nombreComercial = $e($cfg->nombre_comercial ?: $cfg->razon_social);
        $direccion = $e($cfg->direccion_fiscal);
        $ubigeo = $e($cfg->ubigeo);
        $depto = $e($cfg->departamento);
        $prov = $e($cfg->provincia);
        $dist = $e($cfg->distrito);

        return <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2"
  xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
  xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
  xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2">
  <ext:UBLExtensions>
    <ext:UBLExtension>
      <ext:ExtensionContent><!-- Firma digital: se inserta aqui al firmar con Greenter --></ext:ExtensionContent>
    </ext:UBLExtension>
  </ext:UBLExtensions>
  <cbc:UBLVersionID>2.1</cbc:UBLVersionID>
  <cbc:CustomizationID>2.0</cbc:CustomizationID>
  <cbc:ID>{$id}</cbc:ID>
  <cbc:IssueDate>{$fechaEmision}</cbc:IssueDate>
  <cbc:IssueTime>{$horaEmision}</cbc:IssueTime>
  <cbc:InvoiceTypeCode listID="0101">{$tipoDoc}</cbc:InvoiceTypeCode>
  <cbc:DocumentCurrencyCode>{$moneda}</cbc:DocumentCurrencyCode>
  <cac:AccountingSupplierParty>
    <cac:Party>
      <cac:PartyIdentification>
        <cbc:ID schemeID="6">{$ruc}</cbc:ID>
      </cac:PartyIdentification>
      <cac:PartyName>
        <cbc:Name>{$nombreComercial}</cbc:Name>
      </cac:PartyName>
      <cac:PartyLegalEntity>
        <cbc:RegistrationName>{$razon}</cbc:RegistrationName>
        <cac:RegistrationAddress>
          <cbc:ID>{$ubigeo}</cbc:ID>
          <cbc:CityName>{$prov}</cbc:CityName>
          <cbc:CountrySubentity>{$depto}</cbc:CountrySubentity>
          <cbc:District>{$dist}</cbc:District>
          <cac:AddressLine>
            <cbc:Line>{$direccion}</cbc:Line>
          </cac:AddressLine>
        </cac:RegistrationAddress>
      </cac:PartyLegalEntity>
    </cac:Party>
  </cac:AccountingSupplierParty>
  <cac:AccountingCustomerParty>
    <cac:Party>
      <cac:PartyIdentification>
        <cbc:ID schemeID="{$clienteTipoDoc}">{$e($clienteDoc)}</cbc:ID>
      </cac:PartyIdentification>
      <cac:PartyLegalEntity>
        <cbc:RegistrationName>{$e($clienteNombre)}</cbc:RegistrationName>
      </cac:PartyLegalEntity>
    </cac:Party>
  </cac:AccountingCustomerParty>
  <cac:TaxTotal>
    <cbc:TaxAmount currencyID="{$moneda}">{$igv}</cbc:TaxAmount>
    <cac:TaxSubtotal>
      <cbc:TaxableAmount currencyID="{$moneda}">{$subtotal}</cbc:TaxableAmount>
      <cbc:TaxAmount currencyID="{$moneda}">{$igv}</cbc:TaxAmount>
      <cac:TaxCategory>
        <cac:TaxScheme>
          <cbc:ID>1000</cbc:ID>
          <cbc:Name>IGV</cbc:Name>
          <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
        </cac:TaxScheme>
      </cac:TaxCategory>
    </cac:TaxSubtotal>
  </cac:TaxTotal>
  <cac:LegalMonetaryTotal>
    <cbc:LineExtensionAmount currencyID="{$moneda}">{$subtotal}</cbc:LineExtensionAmount>
    <cbc:TaxInclusiveAmount currencyID="{$moneda}">{$total}</cbc:TaxInclusiveAmount>
    <cbc:PayableAmount currencyID="{$moneda}">{$total}</cbc:PayableAmount>
  </cac:LegalMonetaryTotal>{$lineas}
</Invoice>
XML;
    }
}
