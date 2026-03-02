<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Checador</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
body{
height:100vh;
background: linear-gradient(135deg,#6a11cb,#2575fc);
display:flex;
align-items:center;
justify-content:center;
font-family: 'Poppins', sans-serif;
}

.card-app{
background: rgba(255,255,255,0.95);
border-radius:30px;
padding:30px;
box-shadow:0 20px 60px rgba(0,0,0,0.2);
}

.title-app{
font-weight:700;
font-size:26px;
color:#333;
}

.reloj{
font-size:18px;
color:#666;
margin-bottom:20px;
}

.form-control{
border-radius:15px;
padding:12px;
}

.btn-modern{
border-radius:20px;
padding:14px;
font-weight:600;
font-size:16px;
transition:0.3s;
}

.btn-entrada{
background:#00c853;
color:white;
border:none;
}

.btn-salida{
background:#ff3d00;
color:white;
border:none;
}

.btn-modern:hover{
transform:translateY(-3px);
box-shadow:0 10px 20px rgba(0,0,0,0.2);
}

.btn-modern:active{
transform:scale(0.97);
}

.preview-img{
width:100%;
border-radius:20px;
margin-top:10px;
display:none;
}

.check-success{
position:fixed;
top:0;
left:0;
width:100%;
height:100%;
background:rgba(0,0,0,0.5);
display:flex;
align-items:center;
justify-content:center;
font-size:120px;
color:#00c853;
display:none;
}
</style>
</head>

<body>

<div class="col-md-4">
<div class="card-app">

<div class="text-center">
<div class="title-app">
<i class="bi bi-clock-history"></i> Control de Asistencia
</div>
<div class="reloj" id="reloj"></div>
</div>

<form method="POST" action="?url=checador/registrar" enctype="multipart/form-data" onsubmit="mostrarCheck()">

<input type="text" name="codigo" class="form-control mb-3" placeholder="Código de empleado" required>

<input type="password" name="password" class="form-control mb-3" placeholder="Contraseña" required>

<input type="file" name="foto" id="foto" class="form-control mb-2" accept="image/*" capture="environment">

<img id="preview" class="preview-img"/>

<input type="hidden" name="latitud" id="latitud">
<input type="hidden" name="longitud" id="longitud">

<div class="d-grid gap-3 mt-4">

<button type="submit" name="tipo" value="entrada" class="btn btn-modern btn-entrada">
<i class="bi bi-box-arrow-in-right"></i> Registrar Entrada
</button>

<button type="submit" name="tipo" value="salida" class="btn btn-modern btn-salida">
<i class="bi bi-box-arrow-left"></i> Registrar Salida
</button>

</div>

</form>

</div>
</div>

<div class="check-success" id="check">
<i class="bi bi-check-circle-fill"></i>
</div>

<script>
// Reloj en vivo
function actualizarReloj(){
const ahora=new Date();
document.getElementById("reloj").innerText=ahora.toLocaleTimeString();
}
setInterval(actualizarReloj,1000);
actualizarReloj();

// Preview imagen
document.getElementById("foto").addEventListener("change",function(e){
const reader=new FileReader();
reader.onload=function(){
const preview=document.getElementById("preview");
preview.src=reader.result;
preview.style.display="block";
}
reader.readAsDataURL(e.target.files[0]);
});

// GPS
if(navigator.geolocation){
navigator.geolocation.getCurrentPosition(function(pos){
document.getElementById("latitud").value=pos.coords.latitude;
document.getElementById("longitud").value=pos.coords.longitude;
});
}

// Check animación
function mostrarCheck(){
document.getElementById("check").style.display="flex";
setTimeout(()=>{document.getElementById("check").style.display="none";},1000);
}
</script>

</body>
</html>