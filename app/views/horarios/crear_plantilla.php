<!DOCTYPE html>
<html>
<head>
<title>Crear Plantilla</title>

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
    max-width:800px;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}

table th, table td{
    padding:10px;
    border-bottom:1px solid #eee;
    text-align:center;
}

button{
    margin-top:20px;
    background:#16a34a;
    color:white;
    border:none;
    padding:10px 16px;
    border-radius:6px;
    cursor:pointer;
}

input[type="text"], input[type="time"]{
    padding:6px;
    border-radius:6px;
    border:1px solid #ccc;
}
</style>
</head>
<body>

<h2>Crear Nueva Plantilla</h2>

<form method="POST" action="?url=horarios/guardarPlantilla">

<label><strong>Nombre de la plantilla:</strong></label><br>
<input type="text" name="nombre" required>

<table>
<tr>
<th>Día</th>
<th>Entrada</th>
<th>Salida</th>
<th>Descanso</th>
</tr>

<?php
$dias = ["lunes","martes","miercoles","jueves","viernes","sabado","domingo"];
foreach($dias as $dia):
?>
<tr>
<td><?= ucfirst($dia); ?></td>
<td>
<input type="time" 
name="entrada[<?= $dia; ?>]" 
id="entrada_<?= $dia; ?>">
</td>
<td>
<input type="time" 
name="salida[<?= $dia; ?>]" 
id="salida_<?= $dia; ?>">
</td>
<td>
<input type="checkbox" 
name="descanso[<?= $dia; ?>]" 
id="descanso_<?= $dia; ?>"
onclick="toggleDescanso('<?= $dia; ?>')">
</td>
</tr>
<?php endforeach; ?>
</table>

<button type="submit">Guardar Plantilla</button>

<br><br>
<a href="?url=horarios/plantillas">← Volver</a>

</form>

<script>

function toggleDescanso(dia){

    let check = document.getElementById("descanso_" + dia);
    let entrada = document.getElementById("entrada_" + dia);
    let salida = document.getElementById("salida_" + dia);

    if(check.checked){
        entrada.value = "";
        salida.value = "";
        entrada.disabled = true;
        salida.disabled = true;
    } else {
        entrada.disabled = false;
        salida.disabled = false;
    }
}

</script>

</body>
</html>