<!DOCTYPE html>
<html>
<head>
    <title>Crear Sucursal</title>

    <style>
    body{
        font-family: Arial, sans-serif;
        background:#f4f6f9;
        margin:40px;
        color:#333;
    }

    h2{
        margin-bottom:20px;
    }

    form{
        background:white;
        padding:30px;
        border-radius:10px;
        box-shadow:0 10px 25px rgba(0,0,0,0.08);
        max-width:550px;
    }

    label{
        font-weight:600;
        font-size:14px;
    }

    input{
        width:100%;
        padding:10px;
        margin-top:6px;
        margin-bottom:18px;
        border-radius:6px;
        border:1px solid #d1d5db;
        font-size:14px;
    }

    input:focus{
        outline:none;
        border-color:#2563eb;
        box-shadow:0 0 0 2px rgba(37,99,235,0.1);
    }

    button{
        background:#2563eb;
        color:white;
        border:none;
        padding:10px 16px;
        border-radius:6px;
        cursor:pointer;
        font-size:14px;
    }

    button:hover{
        background:#1d4ed8;
    }

    a{
        text-decoration:none;
        color:#2563eb;
        font-weight:500;
    }

    a:hover{
        text-decoration:underline;
    }

    .row{
        display:flex;
        gap:15px;
    }

    .row div{
        flex:1;
    }
    </style>

</head>
<body>

<h2>Crear Nueva Sucursal</h2>

<form method="POST" action="?url=sucursales/guardar">

    <label>Nombre:</label>
    <input type="text" name="nombre" required>

    <label>Dirección:</label>
    <input type="text" name="direccion" required>

    <div class="row">
        <div>
            <label>Latitud:</label>
            <input type="text" name="latitud" required>
        </div>

        <div>
            <label>Longitud:</label>
            <input type="text" name="longitud" required>
        </div>
    </div>

    <div class="row">
        <div>
            <label>Radio (metros):</label>
            <input type="number" name="radio" value="100" required>
        </div>

        <div>
            <label>Tolerancia (minutos):</label>
            <input type="number" name="tolerancia" value="10" required>
        </div>
    </div>

    <button type="submit">Guardar</button>

</form>

<br>
<a href="?url=sucursales">⬅ Volver</a>

</body>
</html>