<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Instrucciones de Llenado - Activos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 30px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .header-logo img {
            width: 150px;
        }

        .header-table td {
            padding: 2px 5px;
            vertical-align: top;
        }

        .title {
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            margin: 20px 0;
            text-decoration: underline;
        }

        h2 {
            font-size: 14px;
            margin-top: 20px;
        }

        ul {
            margin: 5px 0 15px 20px;
        }

        li {
            margin-bottom: 5px;
        }

        table.list-reference {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.list-reference th,
        table.list-reference td {
            border: 1px solid #000;
            padding: 4px;
            font-size: 11px;
            text-align: left;
        }
    </style>
</head>

<body>

    <!-- Encabezado con logo -->
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

    <!-- Título -->
    <div class="title">INSTRUCCIONES PARA LLENADO DE PLANTILLA DE ACTIVOS</div>

    <!-- Columnas de la plantilla -->
    <h2>1. Columnas de la plantilla</h2>
    <ul>
        <li><strong>tag:</strong> Tag o DICO del equipo. OBLIGATORIO.</li>
        <li><strong>equipo:</strong> Tipo de equipo según la lista oficial (ver tabla de referencia).</li>
        <li><strong>marca:</strong> Marca del equipo.</li>
        <li><strong>modelo:</strong> Modelo del equipo.</li>
        <li><strong>serie:</strong> Número de serie del equipo. OBLIGATORIO.</li>
        <li><strong>estado:</strong> Uno de: OPERACION, GARANTIA, SINIESTRO, RESGUARDADO, DANADO, BAJA, OTRO.(El estado
            OPERACION hace referencia a
            los equipos que estan asignados y operando, resguardo son equipos que mantienen resguardado el tecnico de
            informatica))</li>
        <li><strong>propiedad:</strong> Uno de: ALIMENTACION PARA EL BIENESTAR, ARRENDADO, PARTICULAR, OTRO.</li>
        <li><strong>proveedor:</strong> Seleccionar de la lista oficial de proveedores.</li>
        <li><strong>unidad:</strong> Nombre de la Unidad a la que pertenece el departamento (ej. "VALLES CENTRALES").
            OBLIGATORIO.</li>
        <li><strong>departamento:</strong> Nombre del departamento. Si no existe en la unidad especificada, el sistema
            lo creará automáticamente.</li>
        <li><strong>resguardo:</strong> Nombre del empleado que resguarda el equipo. OBLIGATORIO (el formato es nombre,
            apellido paterno y apellido materno).</li>
        <li><strong>activo:</strong> Número de inventario institucional. (Este dato puede permanecer vacio).</li>
    </ul>
    <h2>2. Tipos de equipo validos (columna 'equipo')</h2>
    <table class="list-reference" style="width: 100%; border-collapse: collapse; font-size: 11px;">
        <tbody>
            <tr>
                <td>Access Point</td>
                <td>Biométrico</td>
                <td>Cargador</td>
                <td>Cargador Docking</td>
            </tr>
            <tr>
                <td>CCTV</td>
                <td>Docking</td>
                <td>Equipo All In One</td>
                <td>Equipo Escritorio</td>
            </tr>
            <tr>
                <td>Escritorio Avanzada</td>
                <td>Firewall</td>
                <td>Impresora Multifuncional</td>
                <td>Laptop de Avanzada</td>
            </tr>
            <tr>
                <td>Laptop de Intermedia</td>
                <td>Lector DVD</td>
                <td>Modem Satelital</td>
                <td>Monitor</td>
            </tr>
            <tr>
                <td>Mouse</td>
                <td>No Break</td>
                <td>Portátil</td>
                <td>Proyector</td>
            </tr>
            <tr>
                <td>Router</td>
                <td>Router LTE</td>
                <td>Switch</td>
                <td>Tableta</td>
            </tr>
            <tr>
                <td>Teclado</td>
                <td>Telefonía IP</td>
                <td>Teléfono Analógico</td>
                <td>UPS</td>
            </tr>

        </tbody>

    </table>

    <h2>3. Proveedores válidos (columna 'proveedor')</h2>
    <ul>
        <li>FOCUS</li>
        <li>SYNNEX</li>
        <li>INDUSTRIAS SANDOVAL</li>
        <li>STE</li>
        <li>ALIMENTACION PARA EL BIENESTAR</li>
    </ul>

    <h2>4. Departamentos y almacenes válidos (columna 'departamento')</h2>
    <p>
        A continuación se muestran los <strong>departamentos base</strong> permitidos para la importación.
        Sin embargo, también están permitidos los <strong>departamentos personales</strong> del usuario,
        siempre y cuando correspondan a su <strong>región</strong> y <strong>unidad</strong>.
        El sistema validará automáticamente qué departamentos tiene permitido utilizar cada usuario.
    </p>

    <ul>
        <li>ADMINISTRACION</li>
        <li>CONTABILIDAD</li>
        <li>GERENCIA DE SUCURSAL</li>
        <li>INFORMATICA</li>
        <li>JURIDICO</li>
        <li>LOGISTICA Y TRANSPORTES</li>
        <li>SUBGERENCIA DE OPERACIONES</li>
        <li>PRESUPUESTO</li>
        <li>SUBGERENCIA DE ABASTO</li>
        <li>TESORERIA</li>
    </ul>

    <h2>5. Recomendaciones generales</h2>
    <ul>
        <li>Solo se aceptan archivos CSV, XLS o XLSX.</li>
        <li>No modificar nombres ni el orden de las columnas.</li>
        <li>Completar todas las columnas obligatorias antes de importar.</li>
        <li>Verificar que los departamentos y proveedores coincidan exactamente con las listas oficiales.</li>
        <li>No utilizar signos de puntuación (como puntos o comas) en los textos, ya que pueden interferir con el
            proceso de importación.</li>
    </ul>

</body>

</html>