<!DOCTYPE html>
<html>
<head>
    <title>Registro de Asistencias</title>

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

/* =========================
   BUSCADOR
========================= */

.search-box{
    margin-bottom:20px;
}

.search-box input{
    width:300px;
    padding:8px 12px;
    border-radius:8px;
    border:1px solid #ccc;
}

/* =========================
   TABLA
========================= */

table{
    width:100%;
    border-collapse:separate;
    border-spacing:0;
    background:white;
    border:1px solid #dcdfe4;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
    border-radius:10px;
    overflow:hidden;
}

table th{
    background:#1e293b;
    color:white;
    text-align:left;
    padding:14px;
    font-size:14px;
}

table td{
    padding:14px;
    border-top:1px solid #e5e7eb;
    font-size:14px;
}

table tr:nth-child(even){
    background:#f9fafb;
}

table tr:hover{
    background:#eef2ff;
}

select, button{
    padding:8px 10px;
    border-radius:6px;
    border:1px solid #ccc;
    width:100%;
    margin-top:5px;
}

button{
    background:#2563eb;
    color:white;
    border:none;
    cursor:pointer;
    margin-top:15px;
}

button:hover{
    background:#1d4ed8;
}

/* =========================
   estado
========================= */

.estado-faltas { background:#f87171; color:white; font-weight:bold; }
.estado-puntualidad { background:#4ade80; color:white; font-weight:bold; }
.estado-incapacidad { background:#FFB6C1; color:black; font-weight:bold; }
.estado-vacaciones { background:#facc15; color:white; font-weight:bold; }
.estado-permiso { background:#FFA500; color:white; font-weight:bold; }
.estado-festivo { background:#BCB8C9; color:white; font-weight:bold; }
.estado-descanso { background:#87CEFA; color:white; font-weight:bold; }
.estado-retardo { background:#C19A6B; color:white; font-weight:bold; }
</style>

</head>
<body>

<h2>Registro de Asistencias</h2>

<div class="search-box" style="display:flex; gap:10px; align-items:center;">
    
    🏢 
    <select onchange="filtrarSucursal(this.value)" style="width:200px;">
        <option value="todas">🔄 Todas</option>

        <?php while($s = $sucursales->fetch_assoc()): ?>
            <option value="<?php echo $s['nombre']; ?>">
                <?php echo $s['nombre']; ?>
            </option>
        <?php endwhile; ?>
    </select>

    🔍 
    <input type="text" placeholder="Buscar sucursal..." 
    onkeyup="filtrarSucursalInput(this.value)" style="width:200px;">
</div>

<div class="search-box">
    🔍 <input type="text" id="buscador" placeholder="Buscar empleado..." onkeyup="buscarEmpleado()">
</div>

<select id="filtroEstado" onchange="filtrarEstado()">
  <option value="todas">Todas</option>
  <option value="puntual">Puntual</option>
  <option value="retardo">Retardo</option>
  <option value="fuera_de_rango">Fuera_de_rango</option>
  <option value="falta">Falta</option>
  <option value="descanso">Descanso</option>
  <option value="incapacidad">Incapacidad</option>
  <option value="vacaciones">Vacaciones</option>
  <option value="permiso">Permiso</option>
  <option value="festivo">Festivo</option>
</select>

<table id="tablaAsistencias">
<tr>
    <th>Empleado</th>
    <th>Sucursal</th>
    <th>Fecha</th>
    <th>Entrada</th>
    <th>Salida</th>
    <th>Estado</th>
    <th>Horas</th>
</tr>

<?php if(isset($result) && $result->num_rows > 0): ?>
<?php
$festivos_fijos = ["01-01","02-02","05-01","09-16","11-16","12-25"];

function natalicioBenitoJuarez($year){
    $fecha = new DateTime("$year-03-01");
    $lunes = 0;
    while($lunes < 3){
        if($fecha->format("N") == 1){ $lunes++; }
        if($lunes < 3){ $fecha->modify("+1 day"); }
    }
    return $fecha->format("Y-m-d");
}
?>

<?php while($row = $result->fetch_assoc()): 
$fecha_asistencia = $row["fecha"];
$anio = date("Y", strtotime($fecha_asistencia));
$mes_dia = date("m-d", strtotime($fecha_asistencia));

$es_festivo = in_array($mes_dia, $festivos_fijos) 
              || $fecha_asistencia == natalicioBenitoJuarez($anio);

$estado = $es_festivo ? "festivo" : strtolower(trim($row["estado"]));

switch($estado){
    case "puntual": $claseEstado = "estado-puntualidad"; break;
    case "retardo": $claseEstado = "estado-retardo"; break;
    case "fuera_de_rango": $claseEstado = "estado-incapacidad"; break;
    case "vacaciones": $claseEstado = "estado-vacaciones"; break;
    case "incapacidad": $claseEstado = "estado-incapacidad"; break;
    case "permiso": $claseEstado = "estado-permiso"; break;
    case "festivo": $claseEstado = "estado-festivo"; break;
    case "descanso": $claseEstado = "estado-descanso"; break;
    case "falta": $claseEstado = "estado-faltas"; break;
    default: $claseEstado = ""; break;
}

$horas_trabajadas = "";
if ($row["hora_salida"]) {
    $entrada = strtotime($row["hora_entrada"]);
    $salida = strtotime($row["hora_salida"]);
    $horas = ($salida - $entrada) / 3600;
    $horas_trabajadas = number_format($horas, 2);
}
?>

<tr data-sucursal="<?php echo $row["sucursal_nombre"]; ?>">
    <td><?php echo $row["empleado_nombre"]; ?></td>
    <td><?php echo $row["sucursal_nombre"]; ?></td>
    <td><?php echo date("d/m/Y", strtotime($row["fecha"])); ?></td>
    <td><?php echo $row["hora_entrada"] ? date("H:i:s", strtotime($row["hora_entrada"])) : "—"; ?></td>
    <td><?php echo $row["hora_salida"] ? date("H:i:s", strtotime($row["hora_salida"])) : "—"; ?></td>
    <td class="<?php echo $claseEstado; ?>">
        <?php echo ucfirst($row["estado"]); ?>
    </td>
    <td><?php echo $horas_trabajadas ?: "Pendiente"; ?></td>
</tr>

<?php endwhile; ?>
<?php else: ?>
<tr>
    <td colspan="7">No hay registros de asistencia</td>
</tr>
<?php endif; ?>
</table>

<br>
<a href="?url=dashboard">⬅ Volver al Dashboard</a>

<script>
function aplicarFiltros(){
    let filtroSucursal = window.sucursalActiva || "todas";
    let filtroTexto = document.getElementById("buscador").value.toLowerCase().trim();
    let filtroEstado = document.getElementById("filtroEstado").value.toLowerCase().trim();

    let filas = document.querySelectorAll("#tablaAsistencias tr[data-sucursal]");

    filas.forEach(fila => {

        let sucursal = fila.dataset.sucursal;
        let nombre = fila.children[0].textContent.toLowerCase().trim();
        let estado = fila.children[5].textContent.toLowerCase().trim();

        let coincideSucursal = (
            filtroSucursal === "todas" ||
            sucursal.toLowerCase().includes(filtroSucursal)
        );

        let coincideNombre = nombre.includes(filtroTexto);
        let coincideEstado = (filtroEstado === "todas" || estado === filtroEstado);

        fila.style.display = (coincideSucursal && coincideNombre && coincideEstado) ? "" : "none";
    });
}

function filtrarSucursalInput(valor){
    window.sucursalActiva = valor.toLowerCase();
    aplicarFiltros();
}

function filtrarSucursal(nombre){
    window.sucursalActiva = nombre;
    aplicarFiltros();
}

function buscarEmpleado(){
    aplicarFiltros();
}

function filtrarEstado(){
    aplicarFiltros();
}
</script>

</body>
</html>