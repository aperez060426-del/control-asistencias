<!DOCTYPE html>
<html>
<head>
<title>Horarios</title>

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

.btn{
    display:inline-block;
    padding:8px 14px;
    background:#2563eb;
    color:white;
    border-radius:6px;
    font-size:14px;
    text-decoration:none;
}

.btn:hover{
    background:#1d4ed8;
}

/* TARJETAS EMPLEADOS */
.empleados-container{
    display:flex;
    flex-wrap:wrap;
    gap:15px;
    margin:20px 0;
}

.empleado-card{
    background:white;
    padding:12px 20px;
    border-radius:10px;
    box-shadow:0 8px 20px rgba(0,0,0,0.05);
    cursor:pointer;
    font-weight:600;
    transition:.2s;
}

.empleado-card:hover{
    background:#2563eb;
    color:white;
}

/* BUSCADOR */
.search-box{
    margin:15px 0;
}

.search-box input{
    width:300px;
    padding:8px 12px;
    border-radius:8px;
    border:1px solid #ccc;
}

/* TABLA */
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
    padding:14px;
    text-align:left;
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

.badge-dia{
    background:#e0e7ff;
    padding:4px 8px;
    border-radius:6px;
    font-size:12px;
    font-weight:600;
}
</style>
</head>
<body>

<h2>Listado de Horarios</h2>

<a class="btn" href="?url=horarios/crear">
 Asignar Nuevo Horario
</a>

<?php
require_once "../config/Database.php";
$db = new Database();
$conn = $db->connect();
$empleados = $conn->query("SELECT DISTINCT e.id, e.nombre 
                           FROM empleados e
                           INNER JOIN horarios h ON e.id = h.empleado_id
                           WHERE e.activo = 1
                           ORDER BY e.nombre");
?>

<!-- TARJETAS EMPLEADOS -->
<div class="empleados-container">
<?php while($e = $empleados->fetch_assoc()): ?>
<div class="empleado-card" onclick="filtrarEmpleado('<?php echo $e['nombre']; ?>')">
👤 <?php echo $e["nombre"]; ?>
</div>
<?php endwhile; ?>

<div class="empleado-card" onclick="filtrarEmpleado('todos')">
🔄 Todos
</div>
</div>

<!-- BUSCADOR -->
<div class="search-box">
🔍 <input type="text" id="buscador" placeholder="Buscar empleado..." onkeyup="buscarEmpleado()">
</div>

<table id="tablaHorarios">
<tr>
<th>Empleado</th>
<th>Horario Semanal</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>
<tr data-empleado="<?php echo $row["nombre"]; ?>">
<td><?php echo $row["nombre"]; ?></td>
<td>
<?php 
if($row["horario_semanal"]){
    echo $row["horario_semanal"];
}else{
    echo "Sin horario asignado";
}
?>
</td>
</tr>
<?php endwhile; ?>

</table>

<br>
<a href="?url=dashboard">⬅ Volver al Dashboard</a>

<script>
function filtrarEmpleado(nombre){
let filas = document.querySelectorAll("#tablaHorarios tr[data-empleado]");
filas.forEach(fila=>{
if(nombre === "todos"){
fila.style.display = "";
}else{
fila.style.display = fila.dataset.empleado === nombre ? "" : "none";
}
});
}

function buscarEmpleado(){
let input = document.getElementById("buscador").value.toLowerCase();
let filas = document.querySelectorAll("#tablaHorarios tr[data-empleado]");
filas.forEach(fila=>{
let nombre = fila.children[0].textContent.toLowerCase();
fila.style.display = nombre.includes(input) ? "" : "none";
});
}
</script>

</body>
</html>