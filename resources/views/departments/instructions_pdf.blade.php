<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Instrucciones de Llenado - Estructura Organizacional</title>

    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 30px; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .header-logo img { width: 150px; }
        .header-table td { padding: 2px 5px; vertical-align: top; }

        .title {
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            margin: 20px 0;
            text-decoration: underline;
        }

        h2 { font-size: 14px; margin-top: 20px; }
        ul { margin: 5px 0 15px 20px; }
        li { margin-bottom: 5px; }

        table.list-ref { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.list-ref th, table.list-ref td {
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
        <tr><td colspan="5">UNIDAD DE ADMINISTRACIÓN Y FINANZAS</td></tr>
        <tr><td colspan="5">INFORMÁTICA</td></tr>
        <tr><td colspan="5">SUBGERENCIA DE INFRAESTRUCTURA Y TELECOMUNICACIONES</td></tr>
    </table>

    <!-- Título -->
    <div class="title">INSTRUCCIONES PARA LLENAR LA PLANTILLA DE REGIONES, UNIDADES Y DEPARTAMENTOS</div>

    <!-- Sección 1 -->
    <h2>1. Columnas requeridas en la plantilla</h2>

    <p>La plantilla debe contener obligatoriamente las siguientes columnas:</p>

    <ul>
        <li><strong>regcve:</strong> Clave numérica de la región. OBLIGATORIA.</li>
        <li><strong>regnom:</strong> Nombre de la región. OBLIGATORIA.</li>
        <li><strong>unicve:</strong> Clave numérica de la unidad. OBLIGATORIA.</li>
        <li><strong>uninom:</strong> Nombre de la unidad. OBLIGATORIA.</li>
        <li><strong>areacve:</strong> Clave única del departamento. OBLIGATORIA.</li>
        <li><strong>areanom:</strong> Nombre del departamento. OBLIGATORIA.</li>
        <li><strong>tipo:</strong> Tipo de departamento (Oficina, Almacen u Otro). OBLIGATORIA.</li>
    </ul>

    <p>Las siguientes columnas son <strong>opcionales</strong>, pero recomendadas para un registro completo:</p>

    <ul>
        <li><strong>calle</strong></li>
        <li><strong>colonia</strong></li>
        <li><strong>cp</strong></li>
        <li><strong>municipio</strong></li>
        <li><strong>ciudad</strong></li>
        <li><strong>estado</strong></li>
    </ul>

    <!-- Sección 2 -->
    <h2>2. Estructura mínima requerida</h2>

    <p>La plantilla debe seguir esta estructura lógica por cada fila:</p>

    <ul>
        <li>Una región puede repetirse varias veces (NO duplica datos).</li>
        <li>Una unidad puede repetirse varias veces (NO duplica datos).</li>
        <li>Cada departamento debe tener un <strong>areacve</strong> único.</li>
        <li>Los departamentos se asignan automáticamente a su unidad y región.</li>
    </ul>

    <!-- Sección 3 -->
    <h2>3. Ejemplo de estructura correcta</h2>

    <table class="list-ref">
        <thead>
            <tr>
                <th>regcve</th>
                <th>regnom</th>
                <th>unicve</th>
                <th>uninom</th>
                <th>areacve</th>
                <th>areanom</th>
                <th>tipo</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>47</td>
                <td>OAXACA</td>
                <td>1</td>
                <td>VALLES CENTRALES</td>
                <td>47101</td>
                <td>INFORMATICA</td>
                <td>Oficina</td>
            </tr>
            <tr>
                <td>47</td>
                <td>OAXACA</td>
                <td>1</td>
                <td>VALLES CENTRALES</td>
                <td>47102</td>
                <td>CONTABILIDAD</td>
                <td>Oficina</td>
            </tr>
        </tbody>
    </table>

    <!-- Sección 4 -->
    <h2>4. Reglas importantes</h2>

    <ul>
        <li>No modificar los nombres de las columnas.</li>
        <li>No mezclar regiones diferentes con unidades incorrectas.</li>
        <li>Los tipos permitidos son: <strong>Oficina</strong>, <strong>Almacen</strong>, <strong>Otro</strong>.</li>
        <li>Evitar caracteres especiales o comas dentro de los textos.</li>
        <li>Si una fila tiene datos obligatorios vacíos, el sistema marcará error.</li>
        <li>Las regiones o unidades repetidas NO causan error; el sistema las unifica.</li>
    </ul>

    <!-- Sección 5 -->
    <h2>5. Notas adicionales</h2>

    <ul>
        <li>Solo se aceptan archivos .csv o .xlsx.</li>
        <li>El sistema creará automáticamente direcciones si los campos fueron proporcionados.</li>
        <li>Los departamentos con AREACVE repetido serán actualizados, no duplicados.</li>
    </ul>

</body>
</html>
