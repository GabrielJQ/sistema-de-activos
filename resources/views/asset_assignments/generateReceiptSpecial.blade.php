@php \Carbon\Carbon::setLocale('es'); @endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Vale de Resguardo - {{ $employee->full_name }} - {{ $groupedAssignments->keys()->first() }}</title>
    <style>
        /* ========================= Configuración tamaño carta ========================= */
        @page {
            size: Letter;
            margin: .5cm;
        }
        body {
            font-family: Arial, sans-serif, DejaVu Sans;
            font-size: 11px;
            margin: 0;
            padding: 20px;
        }
        .container { width: 100%; margin: 0 auto; }

        /* ========================= Tablas ========================= */
        table { border-collapse: collapse; width: 100%; }
        .table td, .table th {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: middle;
        }
        .no-border td, .no-border th { border: none !important; }

        /* ========================= Estilos generales ========================= */
        .title { text-align: center; font-weight: bold; font-size: 25px; margin: 20px 0; }
        .bordered-box {
            border: 1px solid #000;
            border-radius: 6px;
            padding: 10px;
            margin-top: 10px;
            font-size: 0.8rem;
        }
        .signature-table td, .signature-table th { border: none !important; }
        .signature-line { border-top: 1px solid #000; width: 80%; margin: 0 auto; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
<div class="container">

    <!-- Logos -->
    <table class="no-border" style="margin-bottom: 20px;">
        <tr>
            <td style="width:25%;"><img src="{{ public_path('images/logoAgricultura.png') }}" alt="Agricultura Logo" style="height:45px; margin-right:15px;"></td>
            <td style="width:25%;"><img src="{{ public_path('images/logoAlimentacionBienestar.png') }}" alt="Logo Bienestar" style="height:50px;"></td>
            <td style="width:50%;"></td>
        </tr>
    </table>

    <!-- Encabezado principal con separación entre tablas internas -->
    <table style="margin-bottom: 20px; width:100%; border-collapse: separate; border-spacing: 5px 0;">
        <tr>
            <!-- Lado izquierdo: datos del empleado -->
            <td style="width: 65%; text-align: left; vertical-align: top; padding-right:5px;">
                <table class="table" style="width:100%; border-collapse: collapse;">
                    <tr>
                        <td><strong>Empleado</strong></td>
                        <td>{{ $employee->full_name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Puesto</strong></td>
                        <td>{{ $employee->puesto ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Departamento</strong></td>
                        <td>{{ $employee->department->areanom ?? '-' }}</td>
                    </tr>
                </table>
            </td>

            <!-- Lado derecho: título institucional -->
            <td style="width: 35%; text-align: right; vertical-align: top; padding-left:5px;">
                <table class="table text-center" style="width:100%; border-collapse: collapse;">
                    <tr><td><strong>ALIMENTACIÓN PARA EL BIENESTAR</strong></td></tr>
                    <tr><td>TARJETA DE CONTROL DE ACTIVOS FIJOS</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Título -->
    <div class="title">VALE DE RESGUARDO</div>

    <!-- Texto encerrado -->
    <div class="bordered-box text-justify" style="line-height:1.5;">
        CON ESTA FECHA HE RECIBIDO DE <strong>ALIMENTACIÓN PARA EL BIENESTAR OAXACA</strong> EL ARTÍCULO MENCIONADO A CONTINUACIÓN,
        PARA USARLO EN LOS TRABAJOS PROPIOS DE MI PUESTO EN LA EMPRESA,
        COMPROMETIÉNDOME A DEVOLVERLO EN EL MOMENTO EN QUE SE ME REQUIERA
        O BIEN LIQUIDARLO A PRECIO ACTUAL EN CASO DE PÉRDIDA.
    </div>

    <!-- Descripción encerrada -->
    <div style="margin-top:20px; font-family: Arial, sans-serif; font-size:14px; line-height:1.5;">
        <strong>Descripción:</strong>
        <div style="border:1px solid #000; border-radius:6px; margin-top:8px; padding:5px; font-family: Arial, sans-serif; font-size:11px; line-height:1.5; min-height:80px;">
            @if(isset($groupedAssignments))
                @foreach($groupedAssignments as $tag => $assignments)
                    @foreach($assignments as $assignment)
                        @php
                            $asset = $assignment->asset;
                            $deviceType = $asset->deviceType->equipo ?? 'Equipo';
                            $marca = strtoupper($asset->marca ?? 'N/A');
                            $modelo = strtoupper($asset->modelo ?? 'N/A');
                            $serie = strtoupper($asset->serie ?? 'N/A');
                        @endphp
                        • {{ $deviceType }}: Marca: {{ $marca }}, Modelo: {{ $modelo }}, Serie: {{ $serie }}<br>
                    @endforeach
                @endforeach
            @else
                Sin asignaciones registradas.
            @endif
        </div>
    </div>

    <!-- Tabla general estructurada -->
    <table class="table text-left" style="margin-top:25px; font-size:11px; line-height:1.5;">
        @if($mainComputer)
            <tr>
                <td colspan="2"><strong>COLOR</strong></td>
                <td colspan="3"><strong>MEDIDAS</strong></td>
                <td colspan="3"><strong>DEPRECIACIÓN ANUAL</strong></td>
            </tr>
            <tr>
                <td colspan="2" style="height:20px;"></td>
                <td colspan="3"></td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td colspan="2"><strong>MODELO</strong></td>
                <td></td>
                <td><strong>CAPACIDAD</strong></td>
                <td colspan="4"><strong>NÚMERO DE SERIE</strong></td>
            </tr>
            <tr>
                <!-- Combina columna 1 y 2, y de 5 a 8 -->
                <td colspan="2" style="border-top:none; height:20px;">{{ strtoupper($mainComputer->asset->modelo ?? 'N/A') }}</td>
                <td style="border-top:none;"></td>
                <td style="border-top:none;"></td>
                <td colspan="4" style="border-top:none;">{{ strtoupper($mainComputer->asset->serie ?? 'N/A') }}</td>
            </tr>
            <tr>
                <td colspan="2"><strong>No. DE PROPIEDAD</strong></td>
                <td></td>
                <td><strong>MARCA</strong></td>
                <td></td>
                <td colspan="2"><strong>FECHA DE ADQUISICIÓN</strong></td>
                <td><strong>IMPORTE</strong></td>
            </tr>
            <tr>
                <!-- Combina columnas 1 y 2 -->
                <td colspan="2" style="border-top:none; height:20px;"></td>
                <td></td>
                <td style="border-top:none;">{{ strtoupper($mainComputer->asset->marca ?? 'N/A') }}</td>
                <td></td>
                <td colspan="2" style="border-top:none;">{{ strtoupper(\Carbon\Carbon::now()->isoFormat('D [de] MMMM [del] YYYY')) }}</td>
                <td style="border-top:none;"></td>
            </tr>
        @else
            <tr>
                <td colspan="2" style="border: 1px solid #000; padding: 4px; text-align:center; font-size: 9px;">
                    No se encontró un equipo principal asignado.
                </td>
            </tr>
        @endif
    </table>

    <!-- Firmas -->
    <table class="signature-table text-center" style="margin-top:60px; width:100%; border-collapse: collapse;">
        <tr>
            <th style="border:none;">RECIBE</th>
            <th style="border:none;">ENTREGÓ</th>
            <th style="border:none;">AUTORIZÓ</th>
        </tr><br><br><br><br><br><br>
        <tr style="height:100px;">
            <!-- Nombres de quien recibe, entrega y autoriza -->
            <td style="vertical-align:bottom; border:none;">
                <div style="margin-bottom:5px;">{{ $employee->full_name }}</div>
                <div style="border-top:1px solid #000; width:80%; margin:0 auto;"></div>
            </td>
            <td style="vertical-align:bottom; border:none;">
                <div style="margin-bottom:5px;">
                    {{ 
                        optional(
                            $employee->department?->unit?->technician?->employee
                        )->full_name ?? ' '
                    }}</div>
                <div style="border-top:1px solid #000; width:80%; margin:0 auto;"></div>
            </td>
            <td style="vertical-align:bottom; border:none;">
                <div style="margin-bottom:5px;">ENCARGADO DE LA ADMINISTRACION Y FINANZAS</div>
                <div style="border-top:1px solid #000; width:80%; margin:0 auto;"></div>
            </td>
        </tr>
        <tr>
            <td style="border:none;"></td>
            <td style="border:none;"></td>
            <td style="border:none;"><strong>ANSELMO LOPEZ MORENO</strong></td>
        </tr>
    </table>


</div>
</body>
</html>
