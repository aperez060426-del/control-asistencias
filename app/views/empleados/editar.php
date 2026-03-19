<!DOCTYPE html>
<html>
<head>
    <title>Editar Empleado</title>

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
        background:#16a34a;
        color:white;
        border:none;
        padding:10px 16px;
        border-radius:6px;
        cursor:pointer;
        font-size:14px;
    }

    button:hover{
        background:#15803d;
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

<h2>Editar Empleado</h2>

<form method="POST" action="/control-asistencias/public/?url=empleados/actualizar">

    <input type="hidden" name="id" value="<?= $empleado['id']; ?>">

    <label>Código:</label>
    <input type="text" name="codigo" value="<?= $empleado['codigo']; ?>" required>

    <label>Nombre:</label>
    <input type="text" name="nombre" value="<?= $empleado['nombre']; ?>" required>

    <label>Nueva Contraseña (opcional):</label>
    <input type="password" name="password">

    <label>Rol:</label>
    <select name="rol">
        <option value="empleado" <?= $empleado['rol'] == 'empleado' ? 'selected' : ''; ?>>Empleado</option>
        <option value="admin" <?= $empleado['rol'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
        <option value="supervisor" <?= $empleado['rol'] == 'supervisor' ? 'selected' : ''; ?>>Supervisor</option>
        <option value="gerente" <?= $empleado['rol'] == 'gerente' ? 'selected' : ''; ?>>Gerente</option>
    </select>

    <label>Sucursal:</label>
    <select name="sucursal_id">
        <?php while($s = $sucursales->fetch_assoc()): ?>
            <option value="<?= $s['id']; ?>"
                <?= $empleado['sucursal_id'] == $s['id'] ? 'selected' : ''; ?>>
                <?= $s['nombre']; ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label>Activo:</label>
    <select name="activo">
        <option value="1" <?= $empleado['activo'] == 1 ? 'selected' : ''; ?>>Sí</option>
        <option value="0" <?= $empleado['activo'] == 0 ? 'selected' : ''; ?>>No</option>
    </select>

    <button type="submit">Actualizar</button>

</form>

<br>
<a href="/control-asistencias/public/?url=empleados">⬅ Volver</a>

</body>
</html>