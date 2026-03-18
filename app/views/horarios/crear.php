<!DOCTYPE html>
<html>
<head>
<title>Asignar Horario</title>

<style>
body{
    font-family: Arial;
    background:#f4f6f9;
    margin:40px;
}

.top-bar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.top-bar a{
    background:#16a34a;
    color:white;
    padding:10px 16px;
    border-radius:6px;
    text-decoration:none;
    font-size:14px;
}

.top-bar a:hover{
    background:#15803d;
}

form{
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
    text-align:center;
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

button:hover{
    background:#1d4ed8;
}

select, input[type="time"]{
    padding:6px;
    border-radius:6px;
    border:1px solid #ccc;
}
.estado-select{
    width:150px;
}
</style>
</head>
<body>

<div class="top-bar">
    <h2>Asignar Horario</h2>
    <a href="?url=horarios/plantillas">Gestionar Plantillas</a>
</div>

<form method="POST" action="?url=horarios/guardar">

<label><strong>Empleado:</strong></label>
<select name="empleado_id" required>
<?php while($e = $empleados->fetch_assoc()): ?>
<option value="<?= $e["id"]; ?>">
<?= $e["nombre"]; ?>
</option>
<?php endwhile; ?>
</select>

<br><br>

<label><strong>Plantilla (opcional):</strong></label>
<select id="plantillaSelect">
<option value="">-- Seleccionar Plantilla --</option>
<?php while($p = $plantillas->fetch_assoc()): ?>
<option value="<?= $p["id"]; ?>">
<?= $p["nombre"]; ?>
</option>
<?php endwhile; ?>
</select>

<button type="button" onclick="cargarPlantilla()">Cargar Plantilla</button>

<table>
<tr>
<th>Día</th>
<th>Entrada</th>
<th>Salida</th>
<th>Estado</th>
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
<select name="estado[<?= $dia; ?>]" 
id="estado_<?= $dia; ?>" 
class="estado-select"
onchange="cambiarEstado('<?= $dia; ?>')">

<option value="normal">Normal</option>
<option value="descanso">Descanso</option>
<option value="vacaciones">Vacaciones</option>
<option value="incapacidad">Incapacidad</option>
<option value="falta">Falta</option>


</select>
</td>

</tr>
<?php endforeach; ?>
</table>

<button type="submit">Guardar Horario</button>

</form>

<script>

function cambiarEstado(dia){

    let estado = document.getElementById("estado_" + dia).value;
    let entrada = document.getElementById("entrada_" + dia);
    let salida = document.getElementById("salida_" + dia);

    if(estado === "normal"){
        entrada.disabled = false;
        salida.disabled = false;
    }else{
        entrada.value = "";
        salida.value = "";
        entrada.disabled = true;
        salida.disabled = true;
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

            if(dia.descanso == 1){
                estado.value = "descanso";
            } 
            else if(dia.descanso == 2){
                estado.value = "vacaciones";
            } 
            else if(dia.descanso == 3){
                estado.value = "incapacidad";
            } 
            else if(dia.descanso == 4){
                estado.value = "falta";
            } 
            else {
                estado.value = "normal";
            }



            if(estado.value === "normal"){
                entrada.disabled = false;
                salida.disabled = false;
                entrada.value = dia.hora_entrada;
                salida.value = dia.hora_salida;
            }else{
                entrada.value = "";
                salida.value = "";
                entrada.disabled = true;
                salida.disabled = true;
            }
        });

    });

}
</script>

</body>
</html>