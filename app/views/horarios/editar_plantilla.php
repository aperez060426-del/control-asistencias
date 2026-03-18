<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar Plantilla</title>

<style>

body{
    margin:0;
    font-family: 'Segoe UI', sans-serif;
    background:#f4f6f9;
}

.container{
    max-width:1000px;
    margin:40px auto;
}

.card{
    background:white;
    border-radius:14px;
    padding:30px;
    box-shadow:0 15px 35px rgba(0,0,0,0.08);
}

h2{
    margin-bottom:25px;
    color:#1e293b;
}

label{
    font-weight:600;
    color:#334155;
}

input[type="text"]{
    width:300px;
    padding:10px;
    border-radius:8px;
    border:1px solid #cbd5e1;
    margin-top:8px;
    margin-bottom:20px;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}

table thead{
    background:#1e293b;
    color:white;
}

table th, table td{
    padding:12px;
    text-align:center;
    border-bottom:1px solid #e2e8f0;
}

table tbody tr:hover{
    background:#f1f5f9;
}

input[type="time"]{
    padding:6px;
    border-radius:6px;
    border:1px solid #cbd5e1;
}

input[type="checkbox"]{
    transform:scale(1.2);
}

.actions{
    margin-top:25px;
}

.btn{
    padding:10px 18px;
    border-radius:8px;
    border:none;
    cursor:pointer;
    font-weight:600;
    margin-right:10px;
}

.btn-primary{
    background:#2563eb;
    color:white;
}

.btn-primary:hover{
    background:#1d4ed8;
}

.btn-secondary{
    background:#64748b;
    color:white;
    text-decoration:none;
    display:inline-block;
}

.btn-secondary:hover{
    background:#475569;
}

</style>
</head>
<body>

<div class="container">

<div class="card">

<h2>Editar Plantilla</h2>

<form method="POST" action="?url=horarios/actualizarPlantilla">

<input type="hidden" name="id" value="<?= $plantilla["id"]; ?>">

<label>Nombre de la plantilla:</label><br>
<input type="text" name="nombre" value="<?= $plantilla["nombre"]; ?>" required>

<table>
<thead>
<tr>
<th>Día</th>
<th>Hora Entrada</th>
<th>Hora Salida</th>
<th>Descanso</th>
</tr>
</thead>
<tbody>

<?php
$dias = ["lunes","martes","miercoles","jueves","viernes","sabado","domingo"];
foreach($dias as $dia):

$detalle = $detalles[$dia] ?? null;
$entrada = $detalle["hora_entrada"] ?? "";
$salida = $detalle["hora_salida"] ?? "";
$descanso = isset($detalle["descanso"]) && $detalle["descanso"] == 1;
?>

<tr>
<td><?= ucfirst($dia); ?></td>
<td>
<input type="time" 
name="entrada[<?= $dia; ?>]" 
value="<?= $entrada; ?>"
<?= $descanso ? "disabled" : ""; ?>>
</td>
<td>
<input type="time" 
name="salida[<?= $dia; ?>]" 
value="<?= $salida; ?>"
<?= $descanso ? "disabled" : ""; ?>>
</td>
<td>
<input type="checkbox" 
name="descanso[<?= $dia; ?>]" 
<?= $descanso ? "checked" : ""; ?>
onclick="toggleDescanso(this)">
</td>
</tr>

<?php endforeach; ?>

</tbody>
</table>

<div class="actions">
<button type="submit" class="btn btn-primary">Actualizar Plantilla</button>
<a href="?url=horarios/plantillas" class="btn btn-secondary">Cancelar</a>
</div>

</form>

</div>
</div>

<script>

function toggleDescanso(checkbox){
    let row = checkbox.closest("tr");
    let inputs = row.querySelectorAll("input[type='time']");
    
    inputs.forEach(input => {
        input.disabled = checkbox.checked;
        if(checkbox.checked){
            input.value = "";
        }
    });
}

</script>

</body>
</html>