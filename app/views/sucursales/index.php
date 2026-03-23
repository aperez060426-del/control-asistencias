<!DOCTYPE html>
<html>
<head>
<title>Sucursales</title>

<style>

.titulo-panel{

    background: rgba(255, 255, 255, 0.3);

    border:1px solid rgba(255,255,255,0.15);

    padding:18px;

    border-radius:12px;

    text-align:center;

    margin-bottom:35px;

    box-shadow:0 10px 25px rgba(0,0,0,0.4);

}

.titulo-panel h2{

    margin:0;

    font-size:24px;

    letter-spacing:1px;

    font-weight:600;

    color:white;

}

.titulo-panel h2{

    margin:0;

    font-size:24px;

    letter-spacing:1px;

    font-weight:600;
    position:relative;   /* necesario para aplicar z-index */
    z-index:10; 

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
    pointer-events: none;
}
/* TITULO */

h2{
    margin-bottom:25px;
}

/* BOTON */

.btn{
    display:inline-block;
    padding:10px 18px;
    background:#2563eb;
    color:white;
    border-radius:8px;
    text-decoration:none;
    margin-bottom:30px;
    position:relative;   /* necesario para aplicar z-index */
    z-index:10;  
}

.btn:hover{
    background: #1d4ed8;
}

/* GRID */

.marcas-container{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:30px;
}

/* TARJETA LOGO */

.marca{
    background: #1118276e;
    border-radius:14px;
    height:120px;

    display:flex;
    align-items:center;
    justify-content:center;

    font-size:22px;
    font-weight:bold;

    cursor:pointer;
    position:relative;

    transition:.25s;

    box-shadow:0 10px 25px rgba(0,0,0,0.4);

    z-index:1;
}

.marca.activa{
    z-index:1000;
}

/* HOVER */

.marca:hover{
    transform:translateY(-4px);
    box-shadow:0 20px 40px rgba(0,0,0,0.6);
}

/* CUANDO METAS LOGOS */

.marca img{
    max-height:60px;
}

/* MENU FLOTANTE */

.menu-sucursales{

    position:absolute;
    top:130px;
    left:0;

    width:280px;

    background:white;
    color:#111;

    border-radius:12px;

    box-shadow:0 15px 40px rgba(0,0,0,0.5);

    padding:10px;

    display:none;

    animation:fade .2s ease;

    z-index:9999;

}

/* ANIMACION */

@keyframes fade{
from{opacity:0; transform:translateY(-10px);}
to{opacity:1; transform:translateY(0);}
}

/* LISTA */

.menu-sucursales ul{
    list-style:none;
    padding:0;
    margin:0;
}

.menu-sucursales li{

    padding:12px;

    border-bottom:1px solid #eee;

}

.menu-sucursales li:last-child{
border:none;
}

/* SUCURSAL */

.sucursal{

    display:flex;

    justify-content:space-between;

    align-items:center;

}

/* NOMBRE */

.sucursal strong{
    font-size:14px;
}

/* DIRECCION */

.sucursal small{
    color:#555;
}

/* ICONOS */

.acciones{
display:flex;
gap:10px;
}

.icono{
cursor:pointer;
font-size:14px;
}

.icono:hover{
transform:scale(1.2);
}

.volver{
    display:inline-block;
    margin-top:40px;
    color:white;
    text-decoration:none;
    font-weight:600;
    position:relative;   /* necesario para aplicar z-index */
    z-index:10; 
}

.volver:hover{
    text-decoration:underline;
}

</style>
</head>

<body>
<div class="video-container">
    <video autoplay muted loop playsinline>
        <source src="video/video.mp4" type="video/mp4">
    </video>
</div>
<div class="overlay"></div>


<div class="titulo-panel">
    <h2>Listado de Sucursales</h2>
</div>

<a class="btn" href="?url=sucursales/crear">➕ Nueva Sucursal</a>

<div class="marcas-container">

<?php

$marcas = ["MVP","MVC","CPR","CAFETERIA","C3G","HOTELES","HSC","ESP","SANTUARIO","ALLORA","CECAP","CORPORATIVO"];

foreach($marcas as $marca):

?>

<div class="marca" onclick="toggleMenu('<?php echo $marca; ?>')">

<img src="imagen/<?php echo $marca; ?>.png" alt="<?php echo $marca; ?>">
<div id="menu-<?php echo $marca; ?>" class="menu-sucursales">

<ul>

<?php

$result->data_seek(0);

while($row = $result->fetch_assoc()):

if($row["marca"] === $marca):

?>

<li>

<div class="sucursal">

<div>

<strong><?php echo $row["nombre"]; ?></strong><br>

<small><?php echo $row["direccion"]; ?></small>

</div>

<div class="acciones">

<a href="?url=sucursales/editar&id=<?php echo $row["id"]; ?>" class="icono">✏️</a>

<a href="?url=sucursales/eliminar&id=<?php echo $row["id"]; ?>" 
class="icono"
onclick="return confirm('¿Eliminar esta sucursal?')">🗑️</a>

</div>

</div>

</li>

<?php endif; endwhile; ?>

</ul>

</div>

</div>

<?php endforeach; ?>

</div>

<a class="volver" href="?url=dashboard">⬅ Volver al Dashboard</a>

<script>

/* CERRAR MENUS ABIERTOS */

function cerrarMenus(){

document.querySelectorAll(".menu-sucursales").forEach(menu=>{

menu.style.display="none"

})

}

/* TOGGLE */

function toggleMenu(marca){

let menu = document.getElementById("menu-"+marca)
let tarjeta = menu.parentElement

if(menu.style.display === "block"){

    menu.style.display = "none"
    tarjeta.classList.remove("activa")

}else{

    cerrarMenus()

    document.querySelectorAll(".marca").forEach(t=>{
        t.classList.remove("activa")
    })

    menu.style.display = "block"
    tarjeta.classList.add("activa")

}

}
/* CERRAR SI DAS CLICK AFUERA */

document.addEventListener("click",function(e){

if(!e.target.closest(".marca")){

cerrarMenus()

}

})



</script>

</body>
</html>