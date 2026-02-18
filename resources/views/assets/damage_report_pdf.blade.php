<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Daño - {{ $asset->serie ?? 'Sin serie' }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #000;
            margin: 15px;
        }

        /* Encabezado de logos */
        .logos-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            border: none;
        }

        .logos-table td {
            padding: 0;
            vertical-align: top;
            border: none; 
        }


        /* Sección principal */
        .section-title {
            font-weight: bold;
            text-align: center;
            background-color: #cccccc;
            color: #000;
            padding: 4px;
            font-size: 13px;
            margin-bottom: 5px;
        }

        /* Sub-encabezados */
        .card-header {
            background-color: #666666;
            color: #fff;
            font-weight: bold;
            padding: 4px 6px;
            font-size: 11px;
        }

        /* Cuerpo de la tarjeta */
        .card-body {
            padding: 4px 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        th, td {
            border: 1px solid #999;
            padding: 4px 6px;
            vertical-align: top;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        /* Imagen */
        .image-container img {
            max-width: 100%;
            max-height: 250px;
            border: 1px solid #aaa;
            display: block;
            margin: 0 auto;
        }

        /* Pie de página */
        .footer {
            margin-top: 6px;
            text-align: right;
            font-size: 10px;
            color: #555;
        }

        html, body {
            height: 100%;
            page-break-after: avoid;
        }

        .image-grid {
            text-align: center; 
            font-size: 0;  
        }

        .image-item {
            display: inline-block;   
            width: 30%;          
            margin: 1%;            
            font-size: 11px;         
            vertical-align: top;     
        }

        .image-item img {
            max-width: 100%;
            height: auto;
            border: 1px solid #aaa;
            display: block;
        }


    </style>
</head>
<body>

    {{-- Encabezado con logos --}}
    <table class="logos-table">
        <tr>
            <td style="text-align: left;">
                <img src="{{ public_path('images/logoAgricultura.png') }}" alt="Agricultura Logo" style="height:40px;">
            </td>
            <td style="text-align: left;">
                <img src="{{ public_path('images/logoAlimentacionBienestar.png') }}" alt="Logo Bienestar" style="height:40px;">
            </td>
            <td style="width: 20%;"></td>
            <td style="text-align: right;">
                <img src="{{ public_path('images/Joven-Mexicana.png') }}" alt="Logo Joven Mexicana" style="height:77px; margin-top:-25px;">
            </td>
        </tr>
    </table>

    {{-- Título principal --}}
    <div class="section-title">REPORTE DE DAÑO DE EQUIPAMIENTO</div>

    {{-- Datos del Equipo --}}
    <div class="card">
        <div class="card-header">Datos del Equipo</div>
        <div class="card-body">
            <table>
                <tr><th>N° Serie</th><td>{{ $asset->serie ?? 'N/A' }}</td></tr>
                <tr><th>I.P.</th><td>{{ $ip ?? 'N/A' }}</td></tr>
                <tr><th>N° Resguardo</th><td>{{ $resguardo ?? 'N/A' }}</td></tr>
                <tr><th>Marca</th><td>{{ $asset->marca ?? 'N/A' }}</td></tr>
                <tr><th>Modelo</th><td>{{ $asset->modelo ?? 'N/A' }}</td></tr>
            </table>
        </div>
    </div>

    {{-- Datos del Usuario --}}
    <div class="card">
        <div class="card-header">Datos del Usuario</div>
        <div class="card-body">
            <table>
                <tr><th>Usuario</th><td>{{ $employee->full_name ?? 'N/A' }}</td></tr>
                <tr><th>Correo</th><td>{{ $employee->email ?? 'N/A' }}</td></tr>
                <tr><th>Centro de Trabajo</th><td>ALIMENTACIÓN PARA EL BIENESTAR, S.A. DE C.V. REGIONAL OAXACA</td></tr>
                <tr><th>Dirección o Ubicación</th><td>{{ $employee->direccion ?? 'N/A' }}</td></tr>
                <tr><th>Piso</th><td>PB</td></tr>
                <tr><th>Adscripción</th><td>{{ $employee->adscriptionGroups->pluck('name')->join(', ') ?? 'N/A' }}</td></tr>
                <tr><th>Área o Departamento</th><td>{{ $employee->department->areanom ?? 'N/A' }}</td></tr>
                <tr><th>Cargo o Puesto</th><td>{{ $employee->puesto ?? 'N/A' }}</td></tr>
                <tr><th>Teléfono</th><td>{{ $employee->telefono ?? 'N/A' }}</td></tr>
                <tr><th>Extensión</th><td>{{ $employee->extension ?? 'N/A' }}</td></tr>
                <tr><th>Horario</th><td>8:00 am a 4:00 pm</td></tr>
            </table>
        </div>
    </div>

    {{-- Solicitud o Falla --}}
    <div class="card">
        <div class="card-header">Solicitud o Falla</div>
        <div class="card-body">
            <p>{{ $description }}</p>
        </div>
    </div>

    {{-- Imagen --}}
    @if(!empty($imagePaths))
        <div class="card">
            <div class="card-header">Evidencia Fotográfica</div>
            <div class="card-body image-grid">
                @foreach($imagePaths as $path)
                    <div class="image-item">
                        <img src="{{ public_path('storage/' . $path) }}" alt="Evidencia del daño">
                    </div>
                @endforeach
            </div>
        </div>
    @endif



    <div class="footer">
        Fecha de generación: {{ $fecha }}
    </div>

</body>
</html>
