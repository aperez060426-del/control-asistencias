<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar Horario</title>

<style>
body{
    font-family: Arial, sans-serif;
    background:#f4f6f9;
    margin:40px;
    color:#333;
}

.container{
    max-width:900px;
    margin:0 auto;
    background:white;
    padding:20px;
    border-radius:10px;
    box-shadow:0 8px 20px rgba(0,0,0,0.05);
}

h2{
    margin-bottom:20px;
}

label{
    font-weight:600;
}

input[type="time"], select{
    padding:6px;
    border-radius:6px;
    border:1px solid #ccc;
    margin-top:4px;
    margin-bottom:10px;
}

input[type="checkbox"]{
    transform:scale(1.2);
    margin-left:5px;
}

.btn{
    display:inline-block;
    padding:8px 14px;
    background:#2563eb;
    color:white;
    border-radius:6px;
    font-size:14px;
    text-decoration:none;
    cursor:pointer;
    border:none;
}

.btn:hover{
    background:#1d4ed8;
}

.actions{
    margin-top:20px;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:15px;
}

table th, table td{
    padding:10px;
    border:1px solid #e5e7eb;
    text-align:center;
}
thead{
    background:#1e293b;
    color:white;
}
</style>
</head>
<body>

<div class="container">

<h2>Editar Horario: <?= htmlspecialchars($empleado["nombre"]); ?></h2>

<form method="POST" action="?url=horarios/guardar">

<input type="hidden" name="empleado_id" value="<?= $empleado["id"]; ?>">

<!-- ================= Plantilla ================= -->
<label><strong>Plantilla (opcional):</strong></label>
<select id="plantillaSelect">
<option value="">-- Seleccionar Plantilla --</option>
<?php if(!empty($plantillas)): ?>
    <?php while($p = $plantillas->fetch_assoc()): ?>
        <option value="<?= $p["id"]; ?>"><?= htmlspecialchars($p["nombre"]); ?></option>
    <?php endwhile; ?>
<?php endif; ?>
</select>
<button type="button" class="btn" onclick="cargarPlantilla()">Cargar Plantilla</button>

<!-- ================= Horarios ================= -->
<table>
<thead>
<tr>
<th>Día</th>
<th>Hora Entrada</th>
<th>Hora Salida</th>
<th>Estado</th>
</tr>
</thead>
<tbody>

<?php
$dias = ["lunes","martes","miercoles","jueves","viernes","sabado","domingo"];
foreach($dias as $dia):
    $h = $horarios[$dia] ?? null;
    $entrada = $h["hora_entrada"] ?? "";
    $salida = $h["hora_salida"] ?? "";
    $descanso = isset($h["descanso"]) ? $h["descanso"] : 0;
    $estado = "normal";
    if($descanso==1) $estado="descanso";
    elseif($descanso==2) $estado="vacaciones";
    elseif($descanso==3) $estado="incapacidad";
    elseif($descanso==4) $estado="falta";
?>
<tr>
<td><?= ucfirst($dia); ?></td>
<td>
<input type="time" name="entrada[<?= $dia; ?>]" id="entrada_<?= $dia; ?>" value="<?= $entrada; ?>" <?= $estado!=="normal"?"disabled":""; ?>>
</td>
<td>
<input type="time" name="salida[<?= $dia; ?>]" id="salida_<?= $dia; ?>" value="<?= $salida; ?>" <?= $estado!=="normal"?"disabled":""; ?>>
</td>
<td>
<select name="estado[<?= $dia; ?>]" id="estado_<?= $dia; ?>" onchange="toggleInputs(this)">
    <option value="normal" <?= $estado==="normal"?"selected":""; ?>>Normal</option>
    <option value="descanso" <?= $estado==="descanso"?"selected":""; ?>>Descanso</option>
    <option value="vacaciones" <?= $estado==="vacaciones"?"selected":""; ?>>Vacaciones</option>
    <option value="incapacidad" <?= $estado==="incapacidad"?"selected":""; ?>>Incapacidad</option>
    <option value="falta" <?= $estado==="falta"?"selected":""; ?>>Permiso</option>
</select>
</td>
</tr>
<?php endforeach; ?>

</tbody>
</table>

<div class="actions">
<button type="submit" class="btn">Actualizar Horario</button>
<a href="?url=horarios" class="btn" style="background:#64748b;">Cancelar</a>
</div>

</form>
</div>

<script>
function toggleInputs(select){
    let row = select.closest("tr");
    let entrada = row.querySelector("input[type='time']:nth-child(1)");
    let salida = row.querySelector("input[type='time']:nth-child(2)");
    if(select.value!=="normal"){
        entrada.value = "";
        salida.value = "";
        entrada.disabled = true;
        salida.disabled = true;
    }else{
        entrada.disabled = false;
        salida.disabled = false;
    }
}

function cargarPlantilla() {
    let plantillaId = document.getElementById("plantillaSelect").value;
    if(!plantillaId) return;

    fetch("?url=horarios/obtenerPlantilla&id=" + plantillaId)
    .then(res => res.json())
    .then(data => {
        data.forEach(dia => {
            let entrada = document.getElementById("entrada_" + dia.dia_semana);
            let salida = document.getElementById("salida_" + dia.dia_semana);
            let estado = document.getElementById("estado_" + dia.dia_semana);

            if(dia.descanso == 1) estado.value="descanso";
            else if(dia.descanso == 2) estado.value="vacaciones";
            else if(dia.descanso == 3) estado.value="incapacidad";
            else if(dia.descanso == 4) estado.value="falta";
            else estado.value="normal";

            if(estado.value==="normal"){
                entrada.disabled=false;
                salida.disabled=false;
                entrada.value=dia.hora_entrada;
                salida.value=dia.hora_salida;
            }else{
                entrada.disabled=true;
                salida.disabled=true;
                entrada.value="";
                salida.value="";
            }
        });
    });
}
</script>

</body>
</html>