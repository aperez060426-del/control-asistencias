<!DOCTYPE html>
<html>
<head>
<title>Asignar Horario Semanal</title>

<style>
body{
    font-family: Arial;
    background:#f4f6f9;
    margin:40px;
}

form{
    background:white;
    padding:30px;
    border-radius:12px;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
    max-width:700px;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}

table th, table td{
    padding:10px;
    border-bottom:1px solid #eee;
}

input[type="time"], select{
    padding:6px;
}

button{
    margin-top:20px;
    background:#2563eb;
    color:white;
    border:none;
    padding:10px 16px;
    border-radius:6px;
    cursor:pointer;
}
</style>
</head>
<body>

<h2>Asignar Horario Semanal</h2>

<form method="POST" action="?url=horarios/guardar">

<label>Empleado:</label>
<select name="empleado_id" required>
<?php while($e = $empleados->fetch_assoc()): ?>
<option value="<?= $e["id"]; ?>">
<?= $e["nombre"]; ?>
</option>
<?php endwhile; ?>
</select>

<table>
<tr>
<th>Día</th>
<th>Entrada</th>
<th>Salida</th>
</tr>

<?php
$dias = ["lunes","martes","miercoles","jueves","viernes","sabado","domingo"];
foreach($dias as $dia):
?>
<tr>
<td><?= ucfirst($dia); ?></td>
<td><input type="time" name="entrada[<?= $dia; ?>]"></td>
<td><input type="time" name="salida[<?= $dia; ?>]"></td>
</tr>
<?php endforeach; ?>
</table>

<button type="submit">Guardar Horario Semanal</button>

</form>

<br>
<a href="?url=horarios">⬅ Volver</a>

</body>
</html>