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
    border-collapse:collapse;
    background:white;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
    border-radius:10px;
    overflow:hidden;
}

thead{
    background:#1e293b;
    color:white;
}

th, td{
    padding:12px;
    text-align:center;
    border-bottom:1px solid #e5e7eb;
    font-size:14px;
}

tbody tr:nth-child(even){
    background:#f9fafb;
}

tbody tr:hover{
    background:#eef2ff;
}

/* BADGES */
.badge-hora{
    background:#22c55e;
    color:white;
    padding:4px 8px;
    border-radius:6px;
    font-size:12px;
    font-weight:600;
}

.badge-vacio{
    color:#9ca3af;
}

.badge-vacaciones{
    background:#c084fc; /* lila */
    color:white;
    padding:4px 8px;
    border-radius:6px;
    font-size:12px;
    font-weight:600;
}

.badge-descanso{
    background:#1d4ed8; /*azul*/
    color:white; 
    font-weight:white; 
    padding:4px 8px;
    border-radius:6px;
    font-size:12px;
    font-weight:600;

}

.badge-incapacidad{
    background:#facc15; /* amarillo */
    color:#000;
    padding:4px 8px;
    border-radius:6px;
    font-size:12px;
    font-weight:600;
}

.badge-falta{
    background:#ef4444; /* rojo */
    color:white;
    padding:4px 8px;
    border-radius:6px;
    font-size:12px;
    font-weight:600;
}

/* TARJETAS SUCURSALES */
.sucursales-container{
    display:flex;
    flex-wrap:wrap;
    gap:15px;
    margin:20px 0;
}

.sucursal-card{
    background:white;
    padding:12px 20px;
    border-radius:10px;
    box-shadow:0 8px 20px rgba(0,0,0,0.05);
    cursor:pointer;
    font-weight:600;
    transition:.2s;
}

.sucursal-card:hover{
    background:#2563eb;
    color:white;
}

</style>
</head>
<body>

<h2>Listado de Horarios</h2>

<a class="btn" href="?url=horarios/crear">
Asignar Nuevo Horario
</a>

<!-- 🔵 TARJETAS SUCURSALES -->
<div class="sucursales-container">
<?php while($s = $sucursales->fetch_assoc()): ?>
<div class="sucursal-card" onclick="filtrarSucursal('<?php echo $s['nombre']; ?>')">
📍 <?php echo $s["nombre"]; ?>
</div>
<?php endwhile; ?>

<div class="sucursal-card" onclick="filtrarSucursal('todas')">
🔄 Todas
</div>
</div>

</div>

<!-- BUSCADOR -->
<div class="search-box">
🔍 <input type="text" id="buscador" placeholder="Buscar empleado..." onkeyup="buscarEmpleado()">
</div>

<table id="tablaHorarios">
<thead>
<tr>
<th>Empleado</th>
<th>Lunes</th>
<th>Martes</th>
<th>Miércoles</th>
<th>Jueves</th>
<th>Viernes</th>
<th>Sábado</th>
<th>Domingo</th>
<th>Acciones</th>
</tr>
</thead>
<tbody>

<?php
$dias = ["lunes","martes","miercoles","jueves","viernes","sabado","domingo"];
if(!empty($empleados)):
foreach($empleados as $id => $emp): ?>

<tr data-sucursal="<?php echo $emp["sucursal_nombre"]; ?>"><td><strong><?php echo $emp["nombre"]; ?></strong></td>

<?php foreach($dias as $dia): ?>
<td>
<?php

    if (isset($emp["horarios"]) && isset($emp["horarios"][$dia])) {

        $h = $emp["horarios"][$dia];

        $descanso = isset($h["descanso"]) ? (int)$h["descanso"] : 0;

      

        // 🔹 Si es algún tipo de descanso
        if ($descanso > 0) {

            switch ($descanso) {
                case 1:
                    echo '<span class="badge-descanso">Descanso</span>';
                    break;
                case 2:
                    echo '<span class="badge-vacaciones">Vacaciones</span>';
                    break;
                case 3:
                    echo '<span class="badge-incapacidad">Incapacidad</span>';
                    break;
                case 4:
                    echo '<span class="badge-falta">Permisos</span>';
                    break;
            }

        } 
        // 🔹 Si NO es descanso pero tiene horas
        elseif (!empty($h["hora_entrada"]) && !empty($h["hora_salida"])) {

            echo '<span class="badge-hora">'
                . htmlspecialchars($h["hora_entrada"])
                . ' - '
                . htmlspecialchars($h["hora_salida"])
                . '</span>';

        } 
        // 🔹 Si existe registro pero no tiene horas
        else {
            echo '<span class="badge-vacio">Sin horario</span>';
        }

    } else {
        echo '<span class="badge-vacio">Sin horario</span>';
    }
    ?>
</td>

<?php endforeach; ?>
    <td>
        <a class="btn" href="?url=horarios/editar&id=<?php echo $id; ?>">
            ✏️ Editar
        </a>
    </td>

</tr>
<?php endforeach; ?>
<?php endif; ?>

</tbody>
</table>

<br>
<a href="?url=dashboard">⬅ Volver al Dashboard</a>

<script>
function filtrarEmpleado(nombre){
let filas = document.querySelectorAll("#tablaHorarios tbody tr");
filas.forEach(fila=>{
if(nombre === "todos"){
fila.style.display = "";
}else{
fila.style.display = fila.dataset.empleado === nombre ? "" : "none";
}
});
}

function filtrarSucursal(nombre){
let filas = document.querySelectorAll("#tablaHorarios tbody tr");

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
let filas = document.querySelectorAll("#tablaHorarios tbody tr");
filas.forEach(fila=>{
let nombre = fila.children[0].textContent.toLowerCase();
fila.style.display = nombre.includes(input) ? "" : "none";
});
}
</script>

</body>
</html>