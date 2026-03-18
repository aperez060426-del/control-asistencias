<!DOCTYPE html>
<html>
<head>
<title>Gestionar Plantillas</title>

<style>
body{
    font-family: Arial;
    background:#f4f6f9;
    margin:40px;
}

.container{
    background:white;
    padding:30px;
    border-radius:12px;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
    max-width:900px;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}

table th, table td{
    padding:10px;
    border-bottom:1px solid #eee;
    text-align:left;
}

.btn{
    padding:8px 14px;
    border-radius:6px;
    text-decoration:none;
    color:white;
    font-size:14px;
}

.btn-crear{
    background:#16a34a;
}

.btn-editar{
    background:#2563eb;
}

.btn-eliminar{
    background:#dc2626;
}
</style>
</head>
<body>

<div class="container">

<h2>Gestionar Plantillas</h2>

<br>

<a href="?url=horarios/crearPlantilla" class="btn btn-crear">
    + Crear Nueva Plantilla
</a>

<table>
<tr>
<th>Nombre</th>
<th>Acciones</th>
</tr>

<?php while($p = $plantillas->fetch_assoc()): ?>
<tr>
<td><?= $p["nombre"]; ?></td>
<td>
<a href="?url=horarios/editarPlantilla&id=<?= $p["id"]; ?>" class="btn btn-editar">Editar</a>
<a href="?url=horarios/eliminarPlantilla&id=<?= $p["id"]; ?>" class="btn btn-eliminar"
   onclick="return confirm('¿Eliminar plantilla?')">
   Eliminar
</a>
</td>
</tr>
<?php endwhile; ?>

</table>

<br><br>

<a href="?url=horarios/crear">← Volver a Asignar Horarios</a>

</div>

</body>
</html>