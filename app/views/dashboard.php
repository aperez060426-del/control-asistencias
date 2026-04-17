

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;
}
.header{
    text-align:center;
    padding:40px 20px;
}


.header img{
    width:240px;
}
.header h1{
    font-weight:300;
    letter-spacing:4px;
    margin-top:110px;
}

/* VIDEO FULLSCREEN */
.video-container{
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100vh;
    overflow:hidden;
    z-index:-1;
}

.video-container video{
    width:100%;
    height:100%;
    object-fit:cover;
}

/* OSCURECER VIDEO */
.overlay{
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100vh;
    background:rgba(0,0,0,0.55);
    z-index:0;
}

/* CONTENIDO CENTRADO */
.content{
    position:relative;
    z-index:1;
    height:100vh;
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
    text-align:center;
    color:white;
}

/* TITULO */
.content h1{
    font-size:48px;
    font-weight:700;
    margin-bottom:10px;
}

.content p{
    font-size:18px;
    margin-bottom:40px;
    opacity:0.9;
}

/* BOTONES FLOTANTES */
.menu-buttons{
    display:flex;
    gap:20px;
    flex-wrap:wrap;
    justify-content:center;
}

.menu-buttons a{
    padding:14px 28px;
    border-radius:50px;
    text-decoration:none;
    font-weight:600;
    background:white;
    color:#111;
    transition:0.3s;
    box-shadow:0 8px 25px rgba(0,0,0,0.3);
}

.menu-buttons a:hover{
    transform:translateY(-5px);
    background:#2563eb;
    color:white;
}

/* BOTON CERRAR SESION */
.logout{
    background:#ef4444 !important;
    color:white !important;
}

.logout:hover{
    background:#dc2626 !important;
}
</style>
</head>

<body>

<!-- VIDEO FULL -->
<div class="video-container">
    <video autoplay muted loop playsinline>
        <source src="/control-asistencias/public/video/video.mp4" type="video/mp4">
    </video>
</div>

<div class="overlay"></div>

<!-- CONTENIDO -->
<div class="content">
    <div class="header">
            <img src="imagen/Grupo-Plaza-2024-blanca.png" type="imagen/png">
        <h1>Control de Asistencias</h1>
        <p>Bienvenido <?php echo $_SESSION["usuario"]["nombre"]; ?> | 
        Rol: <?php echo strtoupper($_SESSION["usuario"]["rol"]); ?>
        </p>
    </div>

    <div class="menu-buttons">

    <?php $rol = $_SESSION["usuario"]["rol"]; ?>

    <?php if ($rol != "supervisor_marca" && $rol != "gerente"): ?>
        <a href="?url=asistencias">Asistencias</a>
    <?php endif; ?>

    <!-- ✅ ESTE SIEMPRE VISIBLE -->
    <a href="?url=empleados">Empleados</a>

    <?php if ($rol != "supervisor_marca"): ?>
        <a class="btn" href="?url=horarios">Horarios</a>
    <?php endif; ?>

    <!-- ✅ ESTE SIEMPRE VISIBLE -->
    <?php if ($rol != "gerente"): ?>
    <a href="?url=sucursales">Sucursales</a>
<?php endif; ?>

   <?php if ($rol != "supervisor_marca" && $rol != "gerente"): ?>
    <a href="?url=reportes">Reportes</a>
<?php endif; ?>

    <a href="?url=auth/logout" class="logout">Cerrar sesión</a>

</div>
</div>

</body>
</html>