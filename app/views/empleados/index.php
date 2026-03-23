<!DOCTYPE html>
<html>
<head>
<title>Empleados</title>

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

/* BOTÓN NUEVO */
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

.accion-editar{
    color:#16a34a;
    font-weight:600;
    margin-right:10px;
}

.accion-eliminar{
    color:#dc2626;
    font-weight:600;
    cursor:pointer;
}
</style>
</head>
<body>

<h2>Listado de Empleados</h2>

<a class="btn"
href="/control-asistencias/public/?url=empleados/crear"
onclick="<?php echo ($_SESSION['usuario']['rol'] == 'gerente') ? "alert('Permisos insuficientes'); return false;" : ''; ?>">
➕ Nuevo Empleado
</a>
<?php
require_once "../config/Database.php";
$db = new Database();
$conn = $db->connect();
$sucursales = $conn->query("SELECT * FROM sucursales");
?>

<!-- 🔥 AQUI VA EL NUEVO -->
<div class="search-box">

<input type="text" placeholder="Buscar sucursal..." onkeyup="filtrarSucursalInput(this.value)">
<select id="selectSucursal" onchange="filtrarSucursal(this.value)">
    <option value="todas">🔄 Todas las sucursales</option>

    <?php while($s = $sucursales->fetch_assoc()): ?>
        <option value="<?php echo $s['nombre']; ?>">
            <?php echo $s['nombre']; ?>
        </option>
    <?php endwhile; ?>

</select>

</div>



<!-- BUSCADOR -->
<div class="search-box">
🔍 <input type="text" id="buscador" placeholder="Buscar empleado..." onkeyup="aplicarFiltros()">
</div>

<table id="tablaEmpleados">
<tr>
<th>ID</th>
<th>Código</th>
<th>Nombre</th>
<th>Rol</th>
<th>Sucursal</th>
<th>Acciones</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>
<tr data-sucursal="<?php echo $row["sucursal_nombre"]; ?>">
<td><?php echo $row["id"]; ?></td>
<td><?php echo $row["codigo"]; ?></td>
<td><?php echo $row["nombre"]; ?></td>
<td><?php echo ucfirst($row["rol"]); ?></td>
<td><?php echo $row["sucursal_nombre"]; ?></td>
<td>

<a class="accion-editar"
href="/control-asistencias/public/?url=empleados/editar/<?php echo $row['id']; ?>"
onclick="<?php echo ($_SESSION['usuario']['rol'] == 'gerente') ? "alert('Permisos insuficientes'); return false;" : ''; ?>">
✏ Editar
</a>

<a class="accion-eliminar"
href="/control-asistencias/public/?url=empleados/eliminar/<?php echo $row['id']; ?>"
onclick="<?php echo ($_SESSION['usuario']['rol'] == 'gerente') 
? "alert('Permisos insuficientes'); return false;" 
: "return confirm('¿Seguro que deseas desactivar este empleado?');"; ?>">
🗑 Eliminar
</a>

</td>
</tr>
<?php endwhile; ?>

</table>

<br>
<a href="/control-asistencias/public/?url=dashboard">⬅ Volver al Dashboard</a>

<script>
    window.sucursalActiva = "todas";
function aplicarFiltros(){
    let filtroSucursal = window.sucursalActiva || "todas";
    let filtroTexto = document.getElementById("buscador").value.toLowerCase().trim();

    let filas = document.querySelectorAll("#tablaEmpleados tr[data-sucursal]");

    filas.forEach(fila => {

        let sucursal = fila.dataset.sucursal.toLowerCase();
        let nombre = fila.children[2].textContent.toLowerCase();

      let coincideSucursal = (
    filtroSucursal === "todas" ||
    sucursal.includes(filtroSucursal)
);



        let coincideNombre = nombre.includes(filtroTexto);

        if(coincideSucursal && coincideNombre){
            fila.style.display = "";
        }else{
            fila.style.display = "none";
        }

    });
}
function filtrarSucursal(nombre){
    window.sucursalActiva = nombre.toLowerCase();
    aplicarFiltros();
}

function filtrarSucursalInput(valor){
    window.sucursalActiva = valor.toLowerCase();
    aplicarFiltros();
}

function filtrarSucursalInput(valor){
    window.sucursalActiva = valor.toLowerCase();
    aplicarFiltros();
}

function filtrarSelect(valor){
let select = document.getElementById("selectSucursal");
let opciones = select.options;

for(let i = 0; i < opciones.length; i++){
    let texto = opciones[i].text.toLowerCase();

    if(texto.includes(valor.toLowerCase()) || opciones[i].value === "todas"){
        opciones[i].style.display = "";
    }else{
        opciones[i].style.display = "none";
    }
}
}

</script>

</body>
</html>