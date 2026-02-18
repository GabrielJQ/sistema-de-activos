<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ 'relacionEmpleados_' . now()->format('Y-m-d_H-i-s') }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 20px; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .header-table td, .header-table th { padding: 5px; text-align: center; }
        .header-logo { width: 180px; }
        .header-logo img { width: 100%; max-width: 180px; }
        .title { text-align: center; font-weight: bold; font-size: 14px; margin: 15px 0 5px; border-bottom: 2px solid #000; padding-bottom: 5px; }
        .footer-info { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .footer-info td { border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold; }

        /* Tabla de datos */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            table-layout: fixed;       /* Ajusta la tabla al ancho de la hoja */
            word-wrap: break-word;     /* Permite que el texto largo haga salto de línea */
        }
        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
            vertical-align: top;
            white-space: normal;       /* Permite multilinea */
            overflow-wrap: break-word;
        }
        .data-table th { background-color: #e0e0e0; font-weight: bold; }
        .data-table tr:nth-child(even) { background-color: #f9f9f9; }
    </style>
</head>
<body>

<!-- Header institucional -->
<table class="header-table">
    <tr>
        <td rowspan="4" class="header-logo">
            <img src="{{ public_path('images/logoAlimentacionBienestar.png') }}" alt="Logo">
        </td>
        <td colspan="5"><strong>ALIMENTACIÓN PARA EL BIENESTAR</strong></td>
    </tr>
    <tr>
        <td colspan="5">UNIDAD DE ADMINISTRACIÓN Y FINANZAS</td>
    </tr>
    <tr>
        <td colspan="5">INFORMÁTICA</td>
    </tr>
    <tr>
        <td colspan="5">SUBGERENCIA DE INFRAESTRUCTURA Y TELECOMUNICACIONES</td>
    </tr>
</table>

<!-- Título del reporte -->
<div class="title">RELACIÓN DE EMPLEADOS</div>

<!-- Folio y Fecha -->
<table class="footer-info">
    <tr>
        <td>Folio: {{ now()->format('Ymd_His') }}</td>
        <td>Fecha: {{ now()->format('d/m/Y') }}</td>
    </tr>
</table>

<!-- Tabla de datos -->
<table class="data-table">
    <thead>
        <tr>
            @foreach(array_keys($data->first()) as $col)
                <th>{{ $col }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($data as $row)
            <tr>
                @foreach($row as $value)
                    <td>{{ $value }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
