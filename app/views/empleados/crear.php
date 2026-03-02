<!DOCTYPE html>
<html>
<head>
    <title>Crear Empleado</title>

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
        max-width:500px;
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
    </style>

</head>
<body>

<h2>Crear Nuevo Empleado</h2>

<form method="POST" action="?url=empleados/guardar">

    <label>Código:</label>
    <input type="text" name="codigo" required>

    <label>Nombre:</label>
    <input type="text" name="nombre" required>

    <label>Contraseña:</label>
    <input type="password" name="password" required>

    <label>Rol:</label>
    <select name="rol" required>
        <option value="empleado">Empleado</option>
        <option value="supervisor">Supervisor</option>
        <option value="admin">Admin</option>
    </select>

    <label>Sucursal:</label>
    <select name="sucursal_id" required>
        <?php while($s = $sucursales->fetch_assoc()): ?>
            <option value="<?php echo $s["id"]; ?>">
                <?php echo $s["nombre"]; ?>
            </option>
        <?php endwhile; ?>
    </select>

    <button type="submit">Guardar</button>

</form>

<br>
<a href="?url=empleados">⬅ Volver</a>

</body>
</html>