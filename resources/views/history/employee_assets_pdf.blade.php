<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ 'relacionActivos_' . $employee->full_name . '_' . now()->format('Y-m-d_H-i-s') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 10px;
        }
        /* Cabecera institucional */
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .header-table td, .header-table th { padding: 5px; text-align: center; }
        .header-logo { width: 180px; }
        .header-logo img { width: 100%; max-width: 180px; }

        /* Título del reporte */
        .title { text-align: center; font-weight: bold; font-size: 14px; margin: 10px 0; border-bottom: 2px solid #000; padding-bottom: 5px; }

        /* Folio y Fecha */
        .footer-info { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .footer-info td { border: 1px solid #000; padding: 4px; text-align: center; font-weight: bold; }

        /* Tabla de datos */
        .data-table { width: 100%; border-collapse: collapse; margin-top: 5px; table-layout: fixed; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 4px; text-align: left; word-wrap: break-word; white-space: normal; max-width: 120px; }
        .data-table th { background-color: #e0e0e0; font-weight: bold; }
        .data-table tr:nth-child(even) { background-color: #f9f9f9; }

        /* Estado en baja */
        .text-danger { color: #dc3545; font-weight: bold; }
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
<div class="title">RELACIÓN DE ACTIVOS DE {{ $employee->full_name }}</div>

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
            <th>Activo</th>
            <th>Tipo</th>
            <th>Serie</th>
            <th>Departamento</th>
            <th>Asignado</th>
            <th>Devuelto</th>
            <th>Observaciones</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $row)
        <tr>
            <td>{{ $row['Activo'] }}</td>
            <td>{{ $row['Tipo'] }}</td>
            <td>{{ $row['Serie'] }}</td>
            <td>{{ $row['Departamento'] }}</td>
            <td>{{ $row['Asignado'] }}</td>
            <td>{{ $row['Devuelto'] }}</td>
            <td>{{ $row['Observaciones'] }}</td>
            <td class="{{ $row['Estado'] === 'Baja' ? 'text-danger' : '' }}">{{ $row['Estado'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
