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
<?php while($row = $result->fetch_assoc()): ?>

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
    <td><?php echo ucfirst($row["estado"]); ?></td>
    <td><?php echo $horas_trabajadas ?: "Pendiente"; ?></td>
    <td>
<td>
    <?php if (!empty($row["foto"])): ?>
        <a href="/control-asistencias/public/uploads/<?php echo htmlspecialchars($row["foto"]); ?>" target="_blank">
            <img src="/control-asistencias/public/uploads/<?php echo htmlspecialchars($row["foto"]); ?>" width="60">
        </a>
    <?php else: ?>
        —
    <?php endif; ?>
</td>

    </td>
</tr>

<?php endwhile; ?>
<?php else: ?>
<tr>
    <td colspan="8">No hay registros de asistencia</td>
</tr>
<?php endif; ?>
</table>

<br><br>

<h3>Registrar Nueva Asistencia</h3>

<form method="POST" action="?url=asistencias/registrar">

<label>Empleado:</label>
<select name="empleado_id" required>
<?php
$empleados = $conn->query("SELECT * FROM empleados WHERE activo = 1");
while($e = $empleados->fetch_assoc()):
?>
<option value="<?php echo $e["id"]; ?>">
    <?php echo $e["nombre"]; ?>
</option>
<?php endwhile; ?>
</select>

<label>Tipo:</label>
<select name="tipo" required>
<option value="entrada">Entrada</option>
<option value="salida">Salida</option>
</select>

<input type="hidden" name="latitud" id="latitud">
<input type="hidden" name="longitud" id="longitud">

<button type="submit">Registrar</button>

</form>

<br>
<a href="?url=dashboard">⬅ Volver al Dashboard</a>

<script>
function filtrarSucursal(nombre){
    let filas = document.querySelectorAll("#tablaAsistencias tr[data-sucursal]");
    filas.forEach(fila=>{
        if(nombre === "todas"){
            fila.style.display = "";
        }else{
            fila.style.display = fila.dataset.sucursal === nombre ? "" : "none";
        }
    });
}

function buscarEmpleado(){
    let input = document.getElementById("buscador").value.toLowerCase();
    let filas = document.querySelectorAll("#tablaAsistencias tr[data-sucursal]");

    filas.forEach(fila=>{
        let nombre = fila.children[0].textContent.toLowerCase();
        fila.style.display = nombre.includes(input) ? "" : "none";
    });
}

if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
        document.getElementById("latitud").value = position.coords.latitude;
        document.getElementById("longitud").value = position.coords.longitude;
    });
}
</script>

</body>
</html>