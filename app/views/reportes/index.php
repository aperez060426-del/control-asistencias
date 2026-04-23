<!DOCTYPE html>
<html>
<head>
    <title>Reportes</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <style>
    body{
        font-family: Arial, sans-serif;
        background:#f4f6f9;
        margin:40px;
        color:#333;
    }

    h2{
        margin-bottom:20px;
    }

    form{
        background:white;
        padding:20px;
        border-radius:10px;
        box-shadow:0 8px 20px rgba(0,0,0,0.08);
        display:flex;
        gap:15px;
        align-items:end;
        flex-wrap:wrap;
    }

    label{
        font-size:13px;
        font-weight:600;
        display:block;
        margin-bottom:5px;
    }

    input[type="date"]{
        padding:8px;
        border-radius:6px;
        border:1px solid #d1d5db;
    }

    button{
        background:#2563eb;
        color:white;
        border:none;
        padding:9px 14px;
        border-radius:6px;
        cursor:pointer;
        font-size:14px;
    }

    button:hover{
        background:#1d4ed8;
    }

    .cards{
        display:flex;
        gap:20px;
        margin-top:30px;
        flex-wrap:wrap;
    }

    .card{
        background:white;
        padding:20px;
        border-radius:10px;
        box-shadow:0 8px 20px rgba(0,0,0,0.08);
        flex:1;
        min-width:180px;
    }

    .card h4{
        margin:0;
        font-size:14px;
        color:#64748b;
    }

    .card p{
        font-size:22px;
        font-weight:bold;
        margin:8px 0 0 0;
    }

    .grafica-container{
        background:white;
        margin-top:30px;
        padding:20px;
        border-radius:10px;
        box-shadow:0 8px 20px rgba(0,0,0,0.08);
    }

    table{
        width:100%;
        border-collapse:separate;
        border-spacing:0;
        background:white;
        margin-top:30px;
        border-radius:10px;
        overflow:hidden;
        box-shadow:0 10px 25px rgba(0,0,0,0.08);
    }

    th{
        background:#1e293b;
        color:white;
        padding:14px;
        text-align:left;
        font-size:14px;
    }

    td{
        padding:14px;
        border-top:1px solid #e5e7eb;
        font-size:14px;
    }

    tr:nth-child(even){
        background:#f9fafb;
    }

    tr:hover{
        background:#eef2ff;
    }

    .estado{
        padding:4px 8px;
        border-radius:6px;
        font-size:12px;
        font-weight:600;
    }

    .puntual{ background: #dcfce7; color: #166534; }
    .retardo{ background:burlywood; color: #854d0e; }
    .fuera{ background: #fee2e2; color: #991b1b; }
    .vacaciones{ background: #f0d980; color:white; }
    .incapacidad{ background: #FFB6C1; color:black; }
    .permiso{background: #fdbe47 ; color:black}
    .festivos{background: #BCB8C9; color:black}
    .descanso{background:lightblue; color: black;}
    .faltas{background: #fd4d4d; color: black;}

    a{
        text-decoration:none;
        color:#2563eb;
        font-weight:500;
    }

    a:hover{
        text-decoration:underline;
    }
    </style>
</head>

<body>

<h2>Reporte de Asistencias</h2>
<form method="GET">
    <input type="hidden" name="url" value="reportes">

    <div>
        <label>Fecha Inicio</label>
        <input type="date" name="fecha_inicio" required
        value="<?php echo $_GET['fecha_inicio'] ?? ''; ?>">
    </div>

    <div>
        <label>Fecha Fin</label>
        <input type="date" name="fecha_fin" required
        value="<?php echo $_GET['fecha_fin'] ?? ''; ?>">
    </div>

    <!-- ✅ NUEVO: SUCURSAL -->
    <div>
        <label>Sucursal</label>
        <select name="sucursal">
            <option value="">Todas</option>
            <?php while($s = $sucursales->fetch_assoc()): ?>
                <option value="<?php echo $s['id']; ?>"
                <?php if(($sucursal ?? '') == $s['id']) echo 'selected'; ?>>
                    <?php echo $s['nombre']; ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <!-- ✅ NUEVO: EMPLEADO -->
    <div>
        <label>Buscar empleado</label>
        <input type="text" name="empleado"
        value="<?php echo $empleado ?? ''; ?>"
        placeholder="Nombre del empleado">
    </div>

    <!-- ✅ NUEVO: ESTADO -->
    <div>
        <label>Filtrar por</label>
        <select name="filtro_estado">
            <option value="">Todos</option>
            <option value="puntual" <?php if(($filtro_estado ?? '')=="puntual") echo "selected"; ?>>Puntual</option>
            <option value="retardo" <?php if(($filtro_estado ?? '')=="retardo") echo "selected"; ?>>Retardo</option>
            <option value="fuera_de_rango" <?php if(($filtro_estado ?? '')=="fuera_de_rango") echo "selected"; ?>>Fuera de rango</option>
            <option value="falta" <?php if(($filtro_estado ?? '')=="falta") echo "selected"; ?>>Falta</option>
            <option value="vacaciones" <?php if(($filtro_estado ?? '')=="vacaciones") echo "selected"; ?>>Vacaciones</option>
            <option value="incapacidad" <?php if(($filtro_estado ?? '')=="incapacidad") echo "selected"; ?>>Incapacidad</option>
            <option value="descanso" <?php if(($filtro_estado ?? '')=="descanso") echo "selected"; ?>>Descanso</option>
        </select>
    </div>

    <button type="submit">Generar Reporte</button>
</form>

<?php
// Lista de festivos fijos
$festivos_fijos = ["01-01","05-01","16-09","25-12"];

if(!function_exists("natalicioBenitoJuarez")){
    function natalicioBenitoJuarez($year){
        $fecha = new DateTime("$year-03-01");
        $lunes = 0;
        while($lunes < 3){
            if($fecha->format("N") == 1){ $lunes++; }
            if($lunes < 3){ $fecha->modify("+1 day"); }
        }
        return $fecha->format("Y-m-d");
    }
}
?>

<?php if($result): ?>

<div class="cards">
    <div class="card">
        <h4>Total Asistencias</h4>
        <p><?php echo $totales["asistencias"]; ?></p>
    </div>

    <div class="card">
        <h4>Puntuales</h4>
        <p><?php echo $totales["puntuales"]; ?></p>
    </div>

    <div class="card">
        <h4>Retardos</h4>
        <p><?php echo $totales["retardos"]; ?></p>
    </div>

    <div class="card">
        <h4>Fuera de Rango</h4>
        <p><?php echo $totales["fuera_de_rango"]; ?></p>
    </div>


    <div class="card">
        <h4>Total Horas</h4>
        <p><?php echo number_format($totales["horas"], 2); ?></p>
    </div>



    
</div>

<div class="grafica-container">
    <h3>Gráfica</h3>
    <canvas id="grafica"></canvas>
</div>

<script>
const ctx = document.getElementById('grafica');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Puntuales', 'Retardos', 'Fuera de Rango'],
        datasets: [{
            label: 'Cantidad',
            data: [
                <?php echo $totales["puntuales"]; ?>,
                <?php echo $totales["retardos"]; ?>,
                <?php echo $totales["fuera_de_rango"]; ?>
            ],
            backgroundColor: [
                '#22c55e',
                '#eab308',
                '#ef4444'
            ]
        }]
    },
    options: {
        responsive:true,
        plugins:{
            legend:{ display:false }
        }
    }
});
</script>

<script>
function exportarExcel(){

    let tabla = document.getElementById("tablaReporte");

    if(!tabla){
        alert("No hay datos para exportar");
        return;
    }

    let datos = [];

    // 🔹 encabezados
    let headers = [];
    let ths = tabla.querySelectorAll("thead th, tr:first-child th");

    if(ths.length === 0){
        ths = tabla.rows[0].cells;
    }

    for(let th of ths){
        headers.push(th.innerText.trim());
    }

    datos.push(headers);

    // 🔹 filas
    for(let i = 1; i < tabla.rows.length; i++){

        let fila = [];
        let celdas = tabla.rows[i].cells;

        for(let j = 0; j < celdas.length; j++){

            let texto = celdas[j].innerText.trim();
            fila.push(texto);
        }

        datos.push(fila);
    }

    // 🔹 crear hoja
    let ws = XLSX.utils.aoa_to_sheet(datos);

    // 🔥 aplicar colores a columna Estado (columna 5 = índice 5)
    for(let i = 1; i < datos.length; i++){

        let estado = datos[i][5] ? datos[i][5].toLowerCase() : "";
        let celdaRef = XLSX.utils.encode_cell({ r:i, c:5 });

        if(!ws[celdaRef]) continue;

        let estilo = {
            font:{ bold:true },
            alignment:{ horizontal:"center", vertical:"center" }
        };

        if(estado.includes("puntual")){
            estilo.fill = { patternType:"solid", fgColor:{ rgb:"DCFCE7" }};
            estilo.font.color = { rgb:"166534" };
        }
        else if(estado.includes("retardo")){
            estilo.fill = { patternType:"solid", fgColor:{ rgb:"DEB887" }};
            estilo.font.color = { rgb:"854D0E" };
        }
        else if(estado.includes("fuera") || estado.includes("falta")){
            estilo.fill = { patternType:"solid", fgColor:{ rgb:"FEE2E2" }};
            estilo.font.color = { rgb:"991B1B" };
        }
        else if(estado.includes("vacaciones")){
            estilo.fill = { patternType:"solid", fgColor:{ rgb:"F0D980" }};
            estilo.font.color = { rgb:"000000" };
        }
        else if(estado.includes("incapacidad")){
            estilo.fill = { patternType:"solid", fgColor:{ rgb:"FFB6C1" }};
            estilo.font.color = { rgb:"000000" };
        }
        else if(estado.includes("permiso")){
            estilo.fill = { patternType:"solid", fgColor:{ rgb:"FDBE47" }};
            estilo.font.color = { rgb:"000000" };
        }
        else if(estado.includes("festivo")){
            estilo.fill = { patternType:"solid", fgColor:{ rgb:"BCB8C9" }};
            estilo.font.color = { rgb:"000000" };
        }
        else if(estado.includes("descanso")){
            estilo.fill = { patternType:"solid", fgColor:{ rgb:"ADD8E6" }};
            estilo.font.color = { rgb:"000000" };
        }

        ws[celdaRef].s = estilo;
    }

    // 🔹 crear libro
    let wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Reporte");

    // 🔹 exportar
    XLSX.writeFile(wb, "Reporte_Asistencias.xlsx");
}
</script>

<h3 style="margin-top:30px;">Detalle</h3>

<table id="tablaReporte">
    <tr>
        <th>Empleado</th>
        <th>Sucursal</th> <!-- ✅ AGREGADO -->
        <th>Fecha</th>
        <th>Entrada</th>
        <th>Salida</th>
        <th>Horas Extra</th>
        <th>Estado</th>
        
    </tr>

    <?php while($row = $result->fetch_assoc()): ?>
        <?php
        $fecha_asistencia = $row["fecha"];
        $anio = date("Y", strtotime($fecha_asistencia));
        $mes_dia = date("m-d", strtotime($fecha_asistencia));

        $es_festivo = in_array($mes_dia, $festivos_fijos) 
                    || $fecha_asistencia == natalicioBenitoJuarez($anio);

        if($es_festivo){
            $estado = "festivo";
        } else {
            $estado = strtolower(trim($row["estado"]));
        }

        switch($estado){
            case "puntual":     $clase = "puntual"; break;
            case "retardo":     $clase = "retardo"; break;
            case "fuera_de_rango": $clase = "fuera"; break;
            case "vacaciones":  $clase = "vacaciones"; break;
            case "incapacidad": $clase = "incapacidad"; break;
            case "permiso":     $clase = "permiso"; break;
            case "festivo":     $clase = "festivos"; break;
            case "descanso":    $clase = "descanso"; break;
            case "falta":       $clase = "faltas"; break;
            default:            $clase = "fuera"; break;
        }
        ?>

        <?php
        $horaEntrada = $row["hora_entrada"] ? strtotime($row["hora_entrada"]) : 0;
        $horaSalida  = $row["hora_salida"] ? strtotime($row["hora_salida"]) : 0;
        $horasJornada = 8 * 3600;
        $horasTrabajadas = max(0, $horaSalida - $horaEntrada);
        $horasExtra = max(0, $horasTrabajadas - $horasJornada);
?>

    <tr>
        <td><?php echo $row["empleado_nombre"]; ?></td>
        <td><?php echo $row["sucursal_nombre"]; ?></td> <!-- ✅ AGREGADO -->
        <td><?php echo $row["fecha"]; ?></td>
        <td><?php echo $row["hora_entrada"] ? date("H:i:s", strtotime($row["hora_entrada"])) : "—"; ?></td>
        <td><?php echo $row["hora_salida"] ? date("H:i:s", strtotime($row["hora_salida"])) : "—"; ?></td>
        <td><?php echo $horasExtra > 0 ? gmdate("H:i:s", $horasExtra) : "00:00:00"; ?></td>
        <td>
            <?php
            $estado = strtolower(trim($row["estado"]));
            switch($estado){
                case "puntual": $clase="puntual"; break;
                case "retardo": $clase="retardo"; break;
                case "fuera_de_rango":
                case "falta": $clase="fuera"; break;
                case "vacaciones": $clase="vacaciones"; break;
                case "incapacidad":
                case "descanso": $clase="incapacidad"; break;
                default: $clase="fuera";
            }
            ?>
            <span class="estado <?php echo $clase; ?>">
                <?php echo ucfirst($row["estado"]); ?>
            </span>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<button onclick="exportarExcel()">📊 Exportar a Excel</button>

<?php endif; ?>

<br>
<a href="?url=dashboard">⬅ Volver al Dashboard</a>

</body>
</html>