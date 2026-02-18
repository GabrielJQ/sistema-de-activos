<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Instrucciones de Llenado - Empleados</title>
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
    <div class="title">INSTRUCCIONES PARA LLENADO DE PLANTILLA DE EMPLEADOS</div>

    <!-- Columnas de la plantilla -->
    <h2>1. Columnas de la plantilla</h2>
    <ul>
        <li><strong>expediente:</strong> Número de expediente del empleado (No de empleado). OBLIGATORIO.</li>
        <li><strong>nombre:</strong> Nombre(s) del empleado. OBLIGATORIO.</li>
        <li><strong>apellido_pat:</strong> Apellido paterno. Puede quedar vacío.</li>
        <li><strong>apellido_mat:</strong> Apellido materno. Puede quedar vacío.</li>
        <li><strong>curp:</strong> CURP del empleado. Obligatorio.</li>
        <li><strong>unidad:</strong> Nombre de la Unidad a la que pertenece el departamento (ej. "VALLES CENTRALES").
            OBLIGATORIO.</li>
        <li><strong>departamento:</strong> Debe coincidir con la lista oficial de departamentos.</li>
        <li><strong>puesto:</strong> Puesto asignado al empleado.</li>
        <li><strong>tipo:</strong> Uno de: SINDICALIZADO, CONFIANZA, EVENTUAL, HONORARIOS.</li>
        <li><strong>email:</strong> Correo institucional, si aplica.</li>
        <li><strong>telefono:</strong> Número de teléfono, si aplica.</li>
        <li><strong>extension:</strong> Extensión telefónica, si aplica.</li>
        <li><strong>status:</strong> Debe contener únicamente “ACTIVO” (hace referencia si el empleado continua
            laborando).</li>
    </ul>

    <!-- Departamentos válidos -->
    <h2>2. Lista de departamentos (columna 'departamento')</h2>

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

    <p>
        Cualquier departamento que no pertenezca a la región y unidad del usuario, o que no exista en el sistema,
        provocará un error en la importación.
    </p>

    <p>
        Después de la importación, se podrán dar de alta nuevos departamentos directamente en el sistema cuando sea
        necesario.
    </p>


    <h2>3. Recomendaciones generales</h2>
    <ul>
        <li>Solo se aceptan archivos CSV, XLS o XLSX.</li>
        <li>No modificar nombres ni el orden de las columnas.</li>
        <li>Completar todas las columnas obligatorias antes de importar.</li>
        <li>Verificar que los departamentos coincidan exactamente con la lista oficial.</li>
        <li>No utilizar signos de puntuación (como puntos o comas) en los textos, ya que pueden interferir con el
            proceso de importación.</li>
    </ul>

</body>

</html>