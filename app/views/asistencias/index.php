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
   TARJETAS DE SUCURSALES
========================= */

.sucursales-container{
    display:flex;
    flex-wrap:wrap;
    gap:15px;
    margin-bottom:30px;
}

.sucursal-card{
    background:white;
    padding:15px 25px;
    border-radius:12px;
    box-shadow:0 8px 20px rgba(0,0,0,0.05);
    cursor:pointer;
    transition:0.2s;
    font-weight:600;
}

.sucursal-card:hover{
    background:#2563eb;
    color:white;
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

form{
    background:white;
    padding:20px;
    border-radius:8px;
    box-shadow:0 8px 20px rgba(0,0,0,0.05);
    max-width:500px;
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

/* rojo */
.estado-faltas { 
    background:#f87171; 
    color:white; 
    font-weight:bold; 
}   

/* verde */
.estado-puntualidad { 
    background:#4ade80; 
    color:white; 
    font-weight:bold; 
} 

/* rosa */
.estado-incapacidad { 
    background:#FFB6C1; 
    color:black; 
    font-weight:bold; 
} 

/* amarrillo */
.estado-vacaciones { 
    background:#facc15; 
    color:white; 
    font-weight:bold; 
} 

/*naranja*/
.estado-permiso { 
    background:#FFA500; 
    color:white; 
    font-weight:bold; 
} 

/* morado */
.estado-festivo{
    background:#BCB8C9; 
    color:white; 
    font-weight:bold; 

}

/* azul */
.estado-descanso{
    background:#87CEFA; 
    color:white; 
    font-weight:bold; 

}


/* camello */
.estado-retardo{
    background:#C19A6B; 
    color:white; 
    font-weight:bold; 

}
</style>

</head>
<body>

<h2>Registro de Asistencias</h2>

<!-- TARJETAS DE SUCURSALES -->
<div class="sucursales-container">
<?php
require_once "../config/Database.php";
$db = new Database();
$conn = $db->connect();
$sucursales = $conn->query("SELECT * FROM sucursales");

while($s = $sucursales->fetch_assoc()):
?>
<div class="sucursal-card" onclick="filtrarSucursal('<?php echo $s['nombre']; ?>')">
    📍 <?php echo $s["nombre"]; ?>
</div>
<?php endwhile; ?>

<div class="sucursal-card" onclick="filtrarSucursal('todas')">
    🔄 Mostrar Todas
</div>
</div>

<!-- BUSCADOR -->
<div class="search-box">
    🔍 <input type="text" id="buscador" placeholder="Buscar empleado..." onkeyup="buscarEmpleado()">
</div>

<!-- Filtro de Estado (arriba de la tabla) -->
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
    <th>Foto</th>
</tr>

<?php if(isset($result) && $result->num_rows > 0): ?>
<?php
// Lista de festivos fijos
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

if($es_festivo){
    $estado = "festivo";
} else {
    $estado = strtolower(trim($row["estado"]));
}

// Asignamos la clase CSS según el estado
switch($estado){
    case "puntual":     $claseEstado = "estado-puntualidad"; break;
    case "retardo":     $claseEstado = "estado-retardo"; break;
    case "fuera_de_rango": $claseEstado = "estado-incapacidad"; break;
    case "vacaciones":  $claseEstado = "estado-vacaciones"; break;
    case "incapacidad": $claseEstado = "estado-incapacidad"; break;
    case "permiso":     $claseEstado = "estado-permiso"; break;
    case "festivo":     $claseEstado = "estado-festivo"; break;
    case "descanso":    $claseEstado = "estado-descanso"; break;
    case "falta":       $claseEstado = "estado-faltas"; break;
    default:            $claseEstado = ""; break;
}
?>

<?php
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
    <!-- Aquí solo una vez el td con clase -->
    <td class="<?php echo $claseEstado; ?>">
        <?php echo ucfirst($row["estado"]); ?>
    </td>
    <td><?php echo $horas_trabajadas ?: "Pendiente"; ?></td>
    <td>
        <?php if (!empty($row["foto"])): ?>
            <a href="/control-asistencias/public/uploads/<?php echo htmlspecialchars($row["foto"]); ?>" target="_blank">
                <img src="/control-asistencias/public/uploads/<?php echo htmlspecialchars($row["foto"]); ?>" width="60">
            </a>
        <?php else: ?>
            —
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr>
    <td colspan="8">No hay registros de asistencia</td>
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

        let coincideSucursal = (filtroSucursal === "todas" || sucursal === filtroSucursal);
        let coincideNombre = nombre.includes(filtroTexto);
        let coincideEstado = (filtroEstado === "todas" || estado === filtroEstado);

        if(coincideSucursal && coincideNombre && coincideEstado){
            fila.style.display = "";
        }else{
            fila.style.display = "none";
        }

    });
}

// Guardamos sucursal activa
function filtrarSucursal(nombre){
    window.sucursalActiva = nombre;
    aplicarFiltros();
}

// Buscador
function buscarEmpleado(){
    aplicarFiltros();
}

// Estado
function filtrarEstado(){
    aplicarFiltros();
}
</script>



</body>
</html>