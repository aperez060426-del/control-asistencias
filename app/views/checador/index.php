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
background: linear-gradient(135deg, #aee1ff, #2575fc);
display:flex;
align-items:center;
flex-direction:column;
justify-content:center;
font-family: 'Poppins', sans-serif;
}

.header {
    text-align: center;
    margin-bottom: 20px;
}

.header img {
    width: 240px;
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

/* 🔥 MENSAJE */
.mensaje-box{
position:fixed;
top:50%;
left:50%;
transform:translate(-50%, -50%);
background:#fff;
padding:30px 40px;
border-radius:20px;
box-shadow:0 15px 40px rgba(0,0,0,0.3);
text-align:center;
display:none;
z-index:999;
animation: aparecer 0.3s ease;
}

@keyframes aparecer{
from{
opacity:0;
transform:translate(-50%, -60%);
}
to{
opacity:1;
transform:translate(-50%, -50%);
}
}

.icono{
font-size:60px;
margin-bottom:10px;
}

.success{
color:#00c853;
}

.error{
color:#ff3d00;
}

.texto-mensaje{
font-size:18px;
font-weight:500;
color:#333;
}
</style>
</head>

<body>

<div class="header">
    <img src="imagen/Grupo-Plaza-2024-blanca.png" alt="Logo Grupo Plaza">
</div>

<?php
$mensaje = "";
$tipo = "";

if (isset($_SESSION["flash_mensaje"])) {
    $mensaje = $_SESSION["flash_mensaje"];
    $tipo = $_SESSION["flash_tipo"] ?? "success";
    unset($_SESSION["flash_mensaje"]);
    unset($_SESSION["flash_tipo"]);
}
?>

<div class="col-md-4">
<div class="card-app">

<div class="text-center">
<div class="title-app">
<i class="bi bi-clock-history"></i> Control de Asistencia
</div>
<div class="reloj" id="reloj"></div>
</div>

<form method="POST" action="?url=checador/registrar">

<input 
type="text" 
name="codigo" 
class="form-control mb-3" 
placeholder="Código de empleado" 
required
>

<!-- ✅ NUEVO: CONTRASEÑA -->
<input 
type="password" 
name="password" 
class="form-control mb-3" 
placeholder="Contraseña" 
required
>

<input type="hidden" name="latitud" id="latitud">
<input type="hidden" name="longitud" id="longitud">

<div class="d-grid gap-3 mt-4">

<button 
type="submit" 
name="tipo" 
value="entrada" 
class="btn btn-modern btn-entrada"
>
<i class="bi bi-box-arrow-in-right"></i> Registrar Entrada
</button>

<button 
type="submit" 
name="tipo" 
value="salida" 
class="btn btn-modern btn-salida"
>
<i class="bi bi-box-arrow-left"></i> Registrar Salida
</button>

</div>

</form>

</div>
</div>

<!-- 🔥 MENSAJE -->
<div class="mensaje-box" id="mensajeBox">
<div id="iconoEstado" class="icono"></div>
<div id="textoMensaje" class="texto-mensaje"></div>
</div>

<script>
let mensajeBackend = "<?php echo $mensaje; ?>";
let tipoBackend = "<?php echo $tipo; ?>";

window.onload = function(){

  // ⏰ reloj
  function actualizarReloj(){
    const ahora = new Date();
    document.getElementById("reloj").innerText = ahora.toLocaleTimeString();
  }

  setInterval(actualizarReloj, 1000);
  actualizarReloj();

  // 📍 GPS
  if(navigator.geolocation){
    navigator.geolocation.getCurrentPosition(
      function(pos){
        document.getElementById("latitud").value = pos.coords.latitude;
        document.getElementById("longitud").value = pos.coords.longitude;
      },
      function(){
        alert("Activa la ubicación para poder registrar asistencia");
      }
    );
  }

  // 🔥 MENSAJE
  if(mensajeBackend !== ""){

    const box = document.getElementById("mensajeBox");
    const texto = document.getElementById("textoMensaje");
    const icono = document.getElementById("iconoEstado");

    const ahora = new Date().toLocaleTimeString();

    texto.innerText = mensajeBackend + " a las " + ahora;

    if(tipoBackend === "success"){
        icono.innerHTML = '<i class="bi bi-check-circle-fill success"></i>';
    }else{
        icono.innerHTML = '<i class="bi bi-x-circle-fill error"></i>';
    }

    box.style.display = "block";

    setTimeout(() => {
        box.style.display = "none";
    }, 3000);
  }

}
</script>

</body>
</html>