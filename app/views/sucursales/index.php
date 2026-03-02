<!DOCTYPE html>
<html>
<head>
    <title>Sucursales</title>

    <style>
    body{
        font-family: Arial, sans-serif;
        background:#f4f6f9;
        margin:40px;
        color:#333;
    }

    h2{
        margin-bottom:15px;
    }

    .btn{
        display:inline-block;
        padding:8px 14px;
        background:#2563eb;
        color:white;
        border-radius:6px;
        font-size:14px;
        text-decoration:none;
    }

    .btn:hover{
        background:#1d4ed8;
    }

    table{
        width:100%;
        border-collapse:separate;
        border-spacing:0;
        background:white;
        border:1px solid #dcdfe4;
        box-shadow:0 10px 25px rgba(0,0,0,0.08);
        border-radius:10px;
        overflow:hidden;
    }

    th{
        background:#1e293b;
        color:white;
        padding:14px;
        text-align:left;
        font-size:14px;
        letter-spacing:.5px;
    }

    td{
        padding:14px;
        border-top:1px solid #e5e7eb;
        font-size:14px;
    }

    tr:nth-child(even){
        background:#f9fafb;
    }

    tr:hover{
        background:#eef2ff;
        transition:0.2s;
    }

    .badge{
        background:#e0f2fe;
        color:#0369a1;
        padding:4px 8px;
        border-radius:6px;
        font-size:12px;
        font-weight:600;
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

<h2>Listado de Sucursales</h2>

<a class="btn" href="?url=sucursales/crear">
    ➕ Nueva Sucursal
</a>

<br><br>

<table>
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Dirección</th>
        <th>Radio</th>
        <th>Tolerancia</th>
    </tr>

    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row["id"]; ?></td>
        <td><strong><?php echo $row["nombre"]; ?></strong></td>
        <td><?php echo $row["direccion"]; ?></td>
        <td>
            <span class="badge">
                <?php echo $row["radio_metros"]; ?> m
            </span>
        </td>
        <td>
            <span class="badge">
                <?php echo $row["tolerancia_minutos"]; ?> min
            </span>
        </td>
    </tr>
    <?php endwhile; ?>

</table>

<br>
<a href="?url=dashboard">⬅ Volver al Dashboard</a>

</body>
</html>