@php
    \Carbon\Carbon::setLocale('es');
@endphp
<!DOCTYPE html>
<html>
<head>
    <title>FORMATO DE LIBERACION - {{ $employee->full_name }} - {{ $groupedAssignments->keys()->first() }}</title>
    <style>
         /* =========================
           Configuración tamaño carta
           ========================= */
        @page {
            size: Letter; /* Tamaño carta: 8.5 x 11 pulgadas */
            margin: .5cm;  /* Ajusta márgenes según necesites */
        }

        body {
            font-family: Arial, sans-serif, DejaVu Sans ;
            font-size: 11px;
            margin: 0;
            padding: 20px;
    
        }
        .container { width: 100%; margin: 0 auto; }
        .page { padding: 20px; box-sizing: border-box; }
        .page-break { page-break-after: always; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header img { height: 40px; }
        .header-text { text-align: right; font-size: 10px; }
        .title { text-align: center; font-size: 12px; font-weight: bold; margin-bottom: 20px; }
        .section-title { font-weight: bold; background-color: #dcdcdc; padding: 5px; margin-bottom: 10px; border: 1px solid #ccc; text-align: center; }
        .info-table td, .asset-table th, .asset-table td { border: 1px solid #ccc; padding: 5px; vertical-align: top; }
        .info-table td:first-child { width: 30%; font-weight: bold; }
        .asset-table th { background-color: #f2f2f2; text-align: center; }
        .signature-area { width: 100%; border-collapse: collapse; margin-top: 50px; }
        .signature-area td { border: 1px solid #000; height: 40px; text-align: center; font-size: 10px; }
        .centered-text { text-align: center; margin-top: 50px; }
        .note { font-style: italic; font-size: 10px; margin-top: 20px; }
        .check-box { display: inline-block; width: 12px; height: 12px; border: 1px solid #000; line-height: 10px; text-align: center; margin-right: 2px; font-size: 10px; }
        .text-justify { text-align: justify; }
        .carta-responsiva-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end; /* todos alineados por la base */
            width: 100%;
            margin-bottom: 30px;
        }
        .carta-date-line { text-align: right; font-size: 12px; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="container">
    {{-- PÁGINA 1: DATOS Y EQUIPO --}}
    <div class="page page-break" style="font-size: 8.5px;">

        {{-- Header reorganizado en 2 filas y 4 columnas --}}
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 3px; font-size: 9px;">
            <tr>
                {{-- Columna 1: Logo (ocupa las 2 filas) --}}
                <td style="width: 20%; border: 1px solid #000; padding: 3px; text-align: center;" rowspan="2">
                    <img src="{{ public_path('images/logoAlimentacionBienestar.png') }}" alt="Logo Bienestar" style="height: 30px;">
                </td>

                {{-- Columna 2 fila 1: Información principal --}}
                <td style="width: 50%; border: 1px solid #000; padding: 3px; text-align: center;">
                    ALIMENTACIÓN PARA EL BIENESTAR<br>
                    UNIDAD DE ADMINISTRACIÓN Y FINANZAS<br>
                    GERENCIA DE SISTEMAS<br>
                    SUBGERENCIA DE INFRAESTRUCTURA Y TELECOMUNICACIONES
                </td>

                {{-- Columna 3 fila 1 --}}
                <td style="width: 15%; border: 1px solid #000; padding: 3px; text-align: center; font-weight: bold;">
                    HOJA
                </td>

                {{-- Columna 4 fila 1 --}}
                <td style="width: 15%; border: 1px solid #000; padding: 3px; text-align: center;">
                    1 DE 2
                </td>
            </tr>
            <tr>
                {{-- Columna 2 fila 2: Título principal --}}
                <td style="width: 50%; border: 1px solid #000; padding: 3px; text-align: center; font-weight: bold;">
                    FORMATO DE ASIGNACIÓN - LIBERACIÓN DE EQUIPAMIENTO INFORMÁTICO
                </td>

                {{-- Columna 3 fila 2 --}}
                <td style="width: 15%; border: 1px solid #000; padding: 3px; text-align: center; font-weight: bold;">
                    Actualización
                </td>

                {{-- Columna 4 fila 2 --}}
                <td style="width: 15%; border: 1px solid #000; padding: 3px; text-align: center;">
                    {{ \Carbon\Carbon::now()->isoFormat('YYYY') }}
                </td>
            </tr>
        </table>

        {{-- Folio y fecha usando tabla para PDF --}}
        <table style="width: 100%; font-size: 9px; margin-bottom: 8px;">
            <tr>
                <td style="text-align: left;">
                    Folio: 
                </td>
                <td style="text-align: right;">
                    Fecha: {{ strtoupper(\Carbon\Carbon::now()->isoFormat('D [de] MMMM [del] YYYY')) }}
                </td>
            </tr>
        </table>

        <p style="margin-bottom: 8px; font-size:10px;">
            El presente formato tiene como objetivo documentar, registrar y resguardar la información de las solicitudes de asignación y baja de equipo tecnológico.
        </p>

        {{-- Secciones principales --}}
        <div style="background-color: #cccccc; color: black; font-size: 14px; padding: 2px; text-align: center;">
            Asignación de equipamiento
        </div>
        <div style="background-color: #666666; color: white; font-size: 10px; padding: 2px; text-align: center;">
            DATOS DEL USUARIO
        </div>

        {{-- Datos del usuario --}}
        <table class="info-table" style="width: 100%; font-size: 11px; border-collapse: collapse; margin-bottom: 5px;">
            <tr><td>Nombre del resguardante</td><td>{{ $employee->full_name }}</td></tr>
            <tr><td>Correo electrónico</td><td>{{ strtolower($employee->email) }}</td></tr>
            <tr><td>Nº Empleado</td><td>{{ $employee->expediente }}</td></tr>
            <tr><td>CURP</td><td>{{ $employee->curp }}</td></tr>
            <tr><td>Centro de Trabajo</td><td>ALIMENTACION PARA EL BIENESTAR, S.A. DE C.V. REGIONAL OAXACA</td></tr>
            <tr><td>Unidad Operativa</td><td>{{ strtoupper($employee->unidad_operativa) ?? 'N/A' }}</td></tr>
            <tr><td>Almacén</td><td>{{ $employee->almacen ?? 'N/A' }}</td></tr>
            <tr><td>Dirección</td><td>{{ $employee->direccion ?? 'N/A' }}</td></tr>
            <tr><td>Piso</td><td>{{ $extraData['piso'] ?? 'PB' }}</td></tr>
            <tr><td>Unidad/ Adscripción</td><td>{{ strtoupper($extraData['unidad_adscripcion'] ?? '') }}</td></tr>
            <tr><td>Departamento</td><td>{{ $employee->department->areanom ?? 'N/A' }}</td></tr>
            <tr><td>Puesto</td><td>{{ $employee->puesto ?? 'N/A' }}</td></tr>
            <tr><td>Teléfono/Extensión</td><td>{{ $employee->telefono ?? 'N/A' }}</td></tr>
            <tr>
                <td>Usuario Final</td>
                <td>
                    @php
                        $assignment = $mainComputer; // O el activo correspondiente dentro de groupedAssignments
                    @endphp

                    @if($assignment->temporaryAssignment)
                        {{strtoupper($assignment->temporaryAssignment->temporary_holder) }}
                    @else
                        {{ $assignment->employee->full_name }}
                    @endif
                </td>
            </tr>


        </table>

        {{-- Datos técnicos --}}
        <div style="background-color: #666666; color: white; font-size: 10px; padding: 2px; text-align: center;">
            <strong>DATOS REQUISITADOS POR EL AREA TECNICA</strong>
        </div>

        {{-- Contenedor principal tabla para ambas secciones --}}
        <table style="width: 100%; border-collapse: collapse; page-break-inside: avoid;">
            <tr>
                {{-- Columna izquierda: Descripción del equipo --}}
                <td style="width: 49%; vertical-align: top; border: none; padding: 0;">
                    <table style="width: 100%; border: 1px solid #000; border-collapse: collapse; font-size: 9px; text-align: center; page-break-inside: avoid;">
                        <tr>
                            <td colspan="2" style="background-color: #666666; color: white; font-size: 10px; padding: 2px; font-weight: bold;">
                                DESCRIPCIÓN DEL EQUIPO
                            </td>
                        </tr>
                        @if($mainComputer)
                        <tr>
                            <td style="width: 40%; border: 1px solid #000; padding: 2px;">Tipo de Equipo</td>
                            <td style="width: 60%; border: 1px solid #000; padding: 2px;">
                                {{ $mainComputer->asset->deviceType?->equipo ?? 'N/A' }}
                            </td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #000; padding: 2px;">Marca</td>
                            <td style="border: 1px solid #000; padding: 2px;">{{ $mainComputer->asset->marca ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #000; padding: 2px;">Modelo</td>
                            <td style="border: 1px solid #000; padding: 2px;">{{ $mainComputer->asset->modelo ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #000; padding: 2px;">Serie</td>
                            <td style="border: 1px solid #000; padding: 2px;">{{ $mainComputer->asset->serie ?? 'N/A' }}</td>
                        </tr>
                        @else
                        <tr>
                            <td colspan="2" style="border: 1px solid #000; padding: 4px; text-align:center; font-size: 9px;">
                                No se encontró un equipo principal asignado.
                            </td>
                        </tr>
                        @endif

                        {{-- Origen del equipo --}}
                        <tr>
                            <td rowspan="2" style="width: 40%; border: 1px solid #000; padding: 2px; background-color: #666666; color: white; font-size: 9px; font-weight: bold;">ORIGEN DEL EQUIPO</td>
                            <td style="width: 60%; border: 1px solid #000; padding: 2px; background-color: #666666; color: white; font-size: 9px; font-weight: bold;">Aplica</td>
                        </tr>
                        <tr>
                            <td style="width: 60%; border: 1px solid #000; padding: 0;">
                                <table style="width: 100%; border-collapse: collapse; text-align: center;">
                                    <tr>
                                        <td style="width: 50%; border-right: 1px solid #000; background-color: #cccccc; color: black; font-size: 9px; padding: 2px;">Propio</td>
                                        <td style="width: 50%; background-color: #cccccc; color: black; font-size: 9px; padding: 2px;">Arrendado</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        {{-- Entidad y palomita --}}
                        <tr>
                            <td style="width: 40%; border: 1px solid #000; padding: 0; height: 20px; vertical-align: middle;">
                                <table style="width: 100%; border-collapse: collapse; text-align: center; table-layout: fixed; height: 20px;">
                                    <tr>
                                        <td style="width: 40%; border: 1px solid #000; padding: 2px; font-size: 9px;">Entidad</td>
                                        <td style="width: 60%; border: 1px solid #000; padding: 2px; font-size: 9px;">ALIMENTACION BIENESTAR</td>
                                    </tr>
                                </table>
                            </td>
                            <td style="width: 60%; border: 1px solid #000; padding: 0; height: 20px; vertical-align: middle;">
                                <table style="width: 100%; border-collapse: collapse; table-layout: fixed; text-align: center; height: 20px;">
                                    <tr>
                                        <td style="width: 50%; border-right: 1px solid #000; padding: 2px; font-size: 9px; line-height: 16px; height: 20px;">
                                            @if(strtoupper(trim($mainComputer->asset->supplier->name ?? '')) === 'ALIMENTACION PARA EL BIENESTAR')
                                                ✔
                                            @endif
                                        </td>
                                        <td style="width: 50%; padding: 2px; font-size: 9px; line-height: 16px; height: 20px;">
                                            @if(strtoupper(trim($mainComputer->asset->supplier->name ?? '')) !== 'ALIMENTACION PARA EL BIENESTAR')
                                                ✔
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        {{-- # Contrato --}}
                        <tr>
                            <td style="width: 40%; border: 1px solid #000; padding: 2px;"># Contrato {{$mainComputer->asset->supplier->prvnombre ?? ''}}</td>
                            <td style="width: 60%; border: 1px solid #000; padding: 2px;">
                                {{ $mainComputer->asset->supplier->contrato ?: $mainComputer->asset->supplier->prvnombre ?? 'N/A' }}
                            </td>
                        </tr>

                        {{-- ACCESORIOS --}}
                        <tr>
                            <td rowspan="2" style="width: 40%; border: 1px solid #000; padding: 2px; background-color: #666666; color: white; font-size: 9px; font-weight: bold;">ACCESORIOS</td>
                            <td style="width: 60%; border: 1px solid #000; padding: 2px; background-color: #666666; color: white; font-size: 9px; font-weight: bold;">Aplica</td>
                        </tr>
                        <tr>
                            <td style="width: 60%; border: 1px solid #000; padding: 0;">
                                <table style="width: 100%; border-collapse: collapse; table-layout: fixed; text-align: center;">
                                    <tr>
                                        <td style="width: 15%; border-left: 1px solid #000; border-right: 1px solid #000; background-color: #cccccc; color: black; font-size: 9px; padding: 2px;">Sí</td>
                                        <td style="width: 15%; border-right: 1px solid #000; background-color: #cccccc; color: black; font-size: 9px; padding: 2px;">No</td>
                                        <td style="width: 70%; background-color: #cccccc; color: black; font-size: 9px; padding: 2px; text-align: left;">(Indicar serie)</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        @php
                            $accesorios = ['Monitor','Teclado','Mouse','Docking','Cargador','Candado','No Break'];
                            $currentAssignments = $groupedAssignments->first(); // <- es el $group del tag actual
                        @endphp

                        @foreach($accesorios as $item)
                        @php
                            $acc = $currentAssignments->first(fn($a) =>
                                strtolower($a->asset->deviceType->equipo ?? '') === strtolower($item)
                            );
                        @endphp
                        <tr>
                            <td style="width: 40%; border: 1px solid #000; padding: 2px;">{{ $item }}</td>
                            <td style="width: 60%; border: 1px solid #000; padding: 0;">
                                <table style="width: 100%; border-collapse: collapse; table-layout: fixed; text-align: center;">
                                    <tr>
                                        <td style="width: 15%; border-left: 1px solid #000; border-right: 1px solid #000; font-size: 10px; padding: 2px; text-align: center;">
                                            {{ $acc ? '✓' : '' }}
                                        </td>
                                        <td style="width: 15%; border-right: 1px solid #000; font-size: 9px; padding: 2px; text-align: center;">&nbsp;</td>
                                        <td style="width: 70%; border-right: 1px solid #000; font-size: 9px; padding: 2px; text-align: left;">
                                            {{ $acc?->asset->serie ?? '' }}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        @endforeach
                    </table>
                </td>

                {{-- Columna derecha: Servicios Incorporados --}}
                <td style="width: 49%; vertical-align: top; border: none; padding-left: 15px;">
                    <table style="width: 100%; border: 1px solid #000; border-collapse: collapse; font-size: 9px; text-align: center; page-break-inside: avoid;">
                        <tr>
                            <td colspan="4" style="background-color: #666666; color: white; font-size: 10px; padding: 2px; text-align: center; font-weight: bold;">
                                SERVICIOS INCORPORADOS
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 70%; background-color: #cccccc; padding: 2px; text-align: center; font-weight: bold;">Servicio</td>
                            <td style="width: 10%; background-color: #cccccc; padding: 2px; text-align: center; font-weight: bold;">Sí</td>
                            <td style="width: 10%; background-color: #cccccc; padding: 2px; text-align: center; font-weight: bold;">No</td>
                            <td style="width: 10%; background-color: #cccccc; padding: 2px; text-align: center; font-weight: bold;">No Aplica</td>
                        </tr>

                        @php
                            $servicios = [
                                'Paqueteria de ofimatica (Office)',
                                'Equipo Opera sin error alguno',
                                'Configuracion de Correo',
                                'Verificacion de Recursos Compartidos',
                                'Transferencia y validacion de respaldo',
                                'Accesos directos',
                                'Lector de PDF',
                                'Configuracion de red e internet',
                                'Instalacion de antivirus',
                                'Fuente Noto Sans',
                                'Impresoras instaladas',
                                'Equipo ingresado a dominio'
                            ];
                        @endphp

                        @foreach($servicios as $servicio)
                        <tr>
                            <td style="width: 70%; border: 1px solid #000; padding: 2px; text-align: left;">
                                {{ $servicio }}
                            </td>
                            <td style="width: 10%; border: 1px solid #000; padding: 2px; text-align: center;">
                                ✔
                            </td>
                            <td style="width: 10%; border: 1px solid #000; padding: 2px;">&nbsp;</td>
                            <td style="width: 10%; border: 1px solid #000; padding: 2px;">&nbsp;</td>
                        </tr>
                        @endforeach


                        <tr>
                            <td style="width: 70%; border: 1px solid #000; padding: 2px; text-align: left;">
                                Hostname del equipo
                            </td>
                            <td colspan="3" style="width: 30%; border: 1px solid #000; padding: 2px;">
                                @php
                                    $defaultHostname = 'OAX-' . strtoupper(explode('@', $employee->email)[0]);
                                    $hostname = $extraData['hostname'] ?? ($mainComputer?->asset?->hostname ?? $defaultHostname);
                                @endphp

                                {{ strtoupper($hostname) }}

                            </td>
                        </tr>

                    </table>
                </td>
            </tr>
        </table>

        

        {{-- Footer --}}
        <p style="font-size: 10px; text-align: left; border-top: 1px solid #ccc; padding-top: 5px;">
            Al firmar este formato el usuario acepta que:
        </p>
        <ul style="list-style-type: circle; font-size: 10px; padding-left: 20px;">
            <li>Es responsabilidad del usuario el resguardo y cuidado del equipo informático y de los accesorios asignados en el presente documento, así como mantenerlos en óptimas condiciones físicas.</li>
        </ul>

    </div>

    {{-- PÁGINA 2: CARTA RESPONSIVA AJUSTADA --}}
    <div class="page">
        {{-- Header para la página 2 --}}
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 3px; font-size: 9px;">
            <tr>
                {{-- Columna 1: Logo (ocupa las 2 filas) --}}
                <td style="width: 20%; border: 1px solid #000; padding: 3px; text-align: center;" rowspan="2">
                    <img src="{{ public_path('images/logoAlimentacionBienestar.png') }}" alt="Logo Bienestar" style="height: 30px;">
                </td>

                {{-- Columna 2 fila 1: Información principal --}}
                <td style="width: 50%; border: 1px solid #000; padding: 3px; text-align: center;">
                    ALIMENTACIÓN PARA EL BIENESTAR<br>
                    UNIDAD DE ADMINISTRACIÓN Y FINANZAS<br>
                    GERENCIA DE SISTEMAS<br>
                    SUBGERENCIA DE INFRAESTRUCTURA Y TELECOMUNICACIONES
                </td>

                {{-- Columna 3 fila 1 --}}
                <td style="width: 15%; border: 1px solid #000; padding: 3px; text-align: center; font-weight: bold;">
                    HOJA
                </td>

                {{-- Columna 4 fila 1 --}}
                <td style="width: 15%; border: 1px solid #000; padding: 3px; text-align: center;">
                    2 DE 2
                </td>
            </tr>
            <tr>
                {{-- Columna 2 fila 2: Título principal --}}
                <td style="width: 50%; border: 1px solid #000; padding: 3px; text-align: center; font-weight: bold;">
                    FORMATO DE ASIGNACIÓN - LIBERACIÓN DE EQUIPAMIENTO INFORMÁTICO
                </td>

                {{-- Columna 3 fila 2 --}}
                <td style="width: 15%; border: 1px solid #000; padding: 3px; text-align: center; font-weight: bold;">
                    Actualización
                </td>

                {{-- Columna 4 fila 2 --}}
                <td style="width: 15%; border: 1px solid #000; padding: 3px; text-align: center;">
                    {{ \Carbon\Carbon::now()->isoFormat('YYYY') }}
                </td>
            </tr>
        </table>

        {{-- Reglas y responsabilidades --}}
        <ul style="font-size: 10px; line-height: 1.2; padding-left: 15px; text-align: justify; margin-bottom: 15px;">
            <li>Es responsabilidad del usuario la entrega de los bienes señalados ante el personal de la Gerencia de Sistemas en cuanto 
                le sea solicitado o concluya el cargo que desempeña. Asimismo, se compromete a realizar todas las gestiones administrativas, para la
                baja y la asigancion de equipamiento, de acuerdo cons los procedimientos normativos vigentes de la Entidad.
            </li>
            <li>Es responsabilidad del usuario, notificar cualquier cambio de ubicación y/o reasignación, reportándolo a la mesa de servicio al tel.: 55 52290769
                o correo electronico: mesadeservicio@segalmex.gob.mx.
            </li>
            <li>En caso de robo o daño (parcial o total) del equipo informatico y/o accesorios asignados, el usuario es responsable de levantar el Acta correspondiente
                ante el Ministerio Publico y debera hacer de conocimiento por escrito adjuntando copia de la respectiva acta a la Gerencia de Sistemas para los efectos 
                administrativos que proceda, asi como el pago del costo total del equipo o bien el pago del deducible en caso de que aplique.
            </li>
            <li>El usuario acepta la responsabilidad sobre el contenido de todos los archivos que se encuentren en el equipo asignado en el presente documento a partir
                de esta fecha, y reconoce que es su deber informar por escrito a sus superiores si tien conocimiento de alguna irregularidad en el equipo, a fin de 
                que se tomen las medidas procedentes.

            </li>
            <li>El usuario acepta los procedimientos contenidos en el Manual de Procedimientos de los Servicios de Tecnologias de la Informacion.</li>
            <li>Los formatos entregados deberán contar con firma autógrafa del jefe inmediato o responsable del área con cargo minimo de subgerente.</li>
            <li>La asignación de equipo está limitada a personal de estructura, honorarios o eventuales que cuenten con numero de empleado de la Entidad.</li>
        </ul>

        <div style="font-weight: bold; margin-bottom: 3px;">Observaciones:</div>
        <div style="border: 1px solid #000; height: 40px; margin-bottom: 20px;"></div>

        {{-- Firmas en columnas con 6 filas --}}
        <table style="width: 100%; border-collapse: collapse; font-size: 9px; margin-bottom: 25px;">

            {{-- Bloque 1: Usuario Solicitante --}}
            <tr>
                <td style="border: 1px solid #000; height: 60px; padding: 0; vertical-align: bottom;"></td>
                <td style="border: 1px solid #000; height: 60px; padding: 0; vertical-align: bottom;"></td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; height: 15px; text-align: center; vertical-align: middle;"></td>
                <td style="border: 1px solid #000; height: 15px; text-align: center; vertical-align: middle;">

                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; height: 20px; text-align: center; vertical-align: middle;">
                    Nombre y Firma del Usuario Solicitante
                </td>
                <td style="border: 1px solid #000; height: 20px; text-align: center; vertical-align: middle;">
                    Nombre, Firma y Cargo del Jefe que Autoriza
                </td>
            </tr>

            {{-- Bloque 2: Proveedor / Técnico --}}
            <tr>
                <td style="border: 1px solid #000; height: 60px; padding: 0; vertical-align: bottom;"></td>
                <td style="border: 1px solid #000; height: 60px; padding: 0; vertical-align: bottom;"></td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; height: 15px; text-align: center; vertical-align: middle;"></td>
                <td style="border: 1px solid #000; height: 15px; text-align: center; vertical-align: middle;">
                    
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; height: 20px; text-align: center; vertical-align: middle;">
                    Nombre y Firma del Proveedor
                </td>
                <td style="border: 1px solid #000; height: 20px; text-align: center; vertical-align: middle;">
                    Nombre y Firma del Técnico
                </td>
            </tr>

        </table>


        {{-- Sección Liberación de Equipo --}}
        <div style="display: flex; flex-direction: column; gap: 0;">
            <div class="section-title" style="background-color: #666666; color: white; font-size: 10px; padding: 2px; font-weight: bold; margin: 0;">
                Sección Para Liberación de Equipo
            </div>
            <div class="section-title" style="background-color: #666666; color: white; font-size: 10px; padding: 2px; font-weight: bold; margin: 0;">
                Prueba de Funcionalidad del Punto De Servicio y Conformidad Estado de Equipo
            </div>
        </div>

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 9px; text-align: center;">
            <!-- Fila de textos descriptivos sin bordes -->
            <tr>
                <td style="width: 10%; padding: 3px; border: none;"></td>
                <td style="width: 10%; padding: 3px; border: none;">SI</td>
                <td style="width: 10%; padding: 3px; border: none;">NO</td>
                <td style="width: 10%; padding: 3px; border: none;"></td>
                <td style="width: 10%; padding: 3px; border: none;">SI</td>
                <td style="width: 10%; padding: 3px; border: none;">NO</td>
            </tr>

            <!-- Fila de Sí/No con bordes solo en estas celdas -->
            <tr>
                <td style="width: 30%; padding: 3px; border: none; text-align: left;">Equipo opera sin error alguno.</td>
                <td style="width: 10%; border: 1px solid #000; padding: 3px;">✔</td>
                <td style="width: 10%; border: 1px solid #000; padding: 3px;"></td>
                <td style="width: 30%; padding: 3px; border: none; text-align: left;">Equipo presenta daños físicos</td>
                <td style="width: 10%; border: 1px solid #000; padding: 3px;"></td>
                <td style="width: 10%; border: 1px solid #000; padding: 3px;">✔</td>
            </tr>
        </table>

        {{-- Firmas Liberación --}}
        <table style="width: 100%; border-collapse: collapse; font-size: 9px; margin-bottom: 25px;">

            {{-- Bloque 1: Usuario Solicitante --}}
            <tr>
                <td style="border: 1px solid #000; height: 60px; padding: 0; vertical-align: bottom;"></td>
                <td style="border: 1px solid #000; height: 60px; padding: 0; vertical-align: bottom;"></td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; height: 15px; text-align: center; vertical-align: middle;">{{ $employee->full_name }}</td>
                <td style="border: 1px solid #000; height: 15px; text-align: center; vertical-align: middle;">
                    {{ strtoupper($extraData['jefe_autoriza_nombre'] ?? '') }}<br>
                    <span style="font-size:8px;">{{ strtoupper($extraData['jefe_autoriza_cargo'] ?? '') }}</span>
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; height: 20px; text-align: center; vertical-align: middle;">
                    Nombre y Firma de Usuario
                </td>
                <td style="border: 1px solid #000; height: 20px; text-align: center; vertical-align: middle;">
                    Nombre, Firma jefe directo
                </td>
            </tr>

            {{-- Bloque 2: Proveedor / Técnico --}}
            <tr>
                <td style="border: 1px solid #000; height: 60px; padding: 0; vertical-align: bottom;"></td>
                <td style="border: 1px solid #000; height: 60px; padding: 0; vertical-align: bottom;"></td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; height: 15px; text-align: center; vertical-align: middle;"></td>
                <td style="border: 1px solid #000; height: 15px; text-align: center; vertical-align: middle;">
                    {{ 
                        optional(
                            $employee->department?->unit?->technician?->employee
                        )->full_name ?? ' '
                    }}
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; height: 20px; text-align: center; vertical-align: middle;">
                    Nombre y Firma del Proveedor
                </td>
                <td style="border: 1px solid #000; height: 20px; text-align: center; vertical-align: middle;">
                    Nombre y Firma del Técnico
                </td>
            </tr>

        </table>
        
    </div>

</div>

</body>
</html>