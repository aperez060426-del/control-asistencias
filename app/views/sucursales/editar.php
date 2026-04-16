<!DOCTYPE html>
<html>
<head>
    <title>Editar Sucursal</title>

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

    input, select{
        width:100%;
        padding:10px;
        margin-top:6px;
        margin-bottom:18px;
        border-radius:6px;
        border:1px solid #d1d5db;
        font-size:14px;
    }

    input:focus, select:focus{
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

<h2>Editar Sucursal</h2>

<form method="POST" action="?url=sucursales/actualizar">

    <!-- ID OCULTO -->
    <!-- ID OCULTO -->
    <input type="hidden" name="id" value="<?= htmlspecialchars($sucursal['id'] ?? '') ?>">

    <label>Marca:</label>
    <select name="marca" required>
    <option value="MVP" <?php if($sucursal['marca']=="MVP") echo "selected"; ?>>MVP</option>
        <option value="MVC" <?php if($sucursal['marca']=="MVC") echo "selected"; ?>>MVC</option>
        <option value="CPR" <?php if($sucursal['marca']=="CPR") echo "selected"; ?>>CPR</option>
        <option value="CAFETERIA" <?php if($sucursal['marca']=="CAFETERIA") echo "selected"; ?>>CAFETERIA</option>
        <option value="C3G" <?php if($sucursal['marca']=="C3G") echo "selected"; ?>>C3G</option>
        <option value="HOTELES" <?php if($sucursal['marca']=="HOTELES") echo "selected"; ?>>HOTELES</option>
        <option value="HSC" <?php if($sucursal['marca']=="HSC") echo "selected"; ?>>HSC</option>
        <option value="ESP" <?php if($sucursal['marca']=="ESP") echo "selected"; ?>>ESP</option>
        <option value="SANTUARIO" <?php if($sucursal['marca']=="SANTUARIO") echo "selected"; ?>>SANTUARIO</option>
        <option value="ALLORA" <?php if($sucursal['marca']=="ALLORA") echo "selected"; ?>>ALLORA</option>
        <option value="CECAP" <?php if($sucursal['marca']=="CECAP") echo "selected"; ?>>CECAP</option>
        <option value="CORPORATIVO" <?php if($sucursal['marca']=="CORPORATIVO") echo "selected"; ?>>CORPORATIVO</option>
        
    </select>

    <label>Nombre:</label>
    <input type="text" name="nombre" value="<?= htmlspecialchars($sucursal['nombre'] ?? '') ?>" required>

    <label>Dirección:</label>
    <input type="text" name="direccion" value="<?= htmlspecialchars($sucursal['direccion'] ?? '') ?>" required>

    <div class="row">
        <div>
            <label>Latitud:</label>
            <input type="text" name="latitud" value="<?= htmlspecialchars($sucursal['latitud'] ?? '') ?>" required>
        </div>
        <div>
            <label>Longitud:</label>
            <input type="text" name="longitud" value="<?= htmlspecialchars($sucursal['longitud'] ?? '') ?>" required>
        </div>
    </div>

    <div class="row">
        <div>
            <label>Radio (metros):</label>
            <input type="number" name="radio" value="<?= htmlspecialchars($sucursal['radio_metros'] ?? '') ?>" required>
        </div>
        <div>
            <label>Tolerancia (minutos):</label>
            <input type="number" name="tolerancia" value="<?= htmlspecialchars($sucursal['tolerancia_minutos'] ?? '') ?>" required>
        </div>
    </div>

    <button type="submit">Actualizar</button>

</form>

<br>
<a href="?url=sucursales">⬅ Volver</a>

</body>
</html>