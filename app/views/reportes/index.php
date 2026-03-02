<!DOCTYPE html>
<html>
<head>
    <title>Reportes</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
        padding:20px;
        border-radius:10px;
        box-shadow:0 8px 20px rgba(0,0,0,0.08);
        display:flex;
        gap:15px;
        align-items:end;
        flex-wrap:wrap;
    }

    label{
        font-size:13px;
        font-weight:600;
        display:block;
        margin-bottom:5px;
    }

    input[type="date"]{
        padding:8px;
        border-radius:6px;
        border:1px solid #d1d5db;
    }

    button{
        background:#2563eb;
        color:white;
        border:none;
        padding:9px 14px;
        border-radius:6px;
        cursor:pointer;
        font-size:14px;
    }

    button:hover{
        background:#1d4ed8;
    }

    .cards{
        display:flex;
        gap:20px;
        margin-top:30px;
        flex-wrap:wrap;
    }

    .card{
        background:white;
        padding:20px;
        border-radius:10px;
        box-shadow:0 8px 20px rgba(0,0,0,0.08);
        flex:1;
        min-width:180px;
    }

    .card h4{
        margin:0;
        font-size:14px;
        color:#64748b;
    }

    .card p{
        font-size:22px;
        font-weight:bold;
        margin:8px 0 0 0;
    }

    .grafica-container{
        background:white;
        margin-top:30px;
        padding:20px;
        border-radius:10px;
        box-shadow:0 8px 20px rgba(0,0,0,0.08);
    }

    table{
        width:100%;
        border-collapse:separate;
        border-spacing:0;
        background:white;
        margin-top:30px;
        border-radius:10px;
        overflow:hidden;
        box-shadow:0 10px 25px rgba(0,0,0,0.08);
    }

    th{
        background:#1e293b;
        color:white;
        padding:14px;
        text-align:left;
        font-size:14px;
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
    }

    .estado{
        padding:4px 8px;
        border-radius:6px;
        font-size:12px;
        font-weight:600;
    }

    .puntual{ background:#dcfce7; color:#166534; }
    .retardo{ background:#fef9c3; color:#854d0e; }
    .fuera{ background:#fee2e2; color:#991b1b; }

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

<h2>Reporte de Asistencias</h2>

<form method="GET">
    <input type="hidden" name="url" value="reportes">

    <div>
        <label>Fecha Inicio</label>
        <input type="date" name="fecha_inicio" required>
    </div>

    <div>
        <label>Fecha Fin</label>
        <input type="date" name="fecha_fin" required>
    </div>

    <button type="submit">Generar Reporte</button>
</form>

<?php if($result): ?>

<div class="cards">
    <div class="card">
        <h4>Total Asistencias</h4>
        <p><?php echo $totales["asistencias"]; ?></p>
    </div>

    <div class="card">
        <h4>Puntuales</h4>
        <p><?php echo $totales["puntuales"]; ?></p>
    </div>

    <div class="card">
        <h4>Retardos</h4>
        <p><?php echo $totales["retardos"]; ?></p>
    </div>

    <div class="card">
        <h4>Fuera de Rango</h4>
        <p><?php echo $totales["fuera_de_rango"]; ?></p>
    </div>

    <div class="card">
        <h4>Total Horas</h4>
        <p><?php echo number_format($totales["horas"], 2); ?></p>
    </div>
</div>

<div class="grafica-container">
    <h3>Gráfica</h3>
    <canvas id="grafica"></canvas>
</div>

<script>
const ctx = document.getElementById('grafica');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Puntuales', 'Retardos', 'Fuera de Rango'],
        datasets: [{
            label: 'Cantidad',
            data: [
                <?php echo $totales["puntuales"]; ?>,
                <?php echo $totales["retardos"]; ?>,
                <?php echo $totales["fuera_de_rango"]; ?>
            ],
            backgroundColor: [
                '#22c55e',
                '#eab308',
                '#ef4444'
            ]
        }]
    },
    options: {
        responsive:true,
        plugins:{
            legend:{ display:false }
        }
    }
});
</script>

<h3 style="margin-top:30px;">Detalle</h3>

<table>
    <tr>
        <th>Empleado</th>
        <th>Fecha</th>
        <th>Entrada</th>
        <th>Salida</th>
        <th>Estado</th>
    </tr>

    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row["empleado_nombre"]; ?></td>
        <td><?php echo $row["fecha"]; ?></td>
        <td><?php echo $row["hora_entrada"]; ?></td>
        <td><?php echo $row["hora_salida"] ?? "—"; ?></td>
        <td>
            <?php
                $estado = $row["estado"];
                $clase = $estado == "puntual" ? "puntual" :
                         ($estado == "retardo" ? "retardo" : "fuera");
            ?>
            <span class="estado <?php echo $clase; ?>">
                <?php echo ucfirst($estado); ?>
            </span>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<?php endif; ?>

<br>
<a href="?url=dashboard">⬅ Volver al Dashboard</a>

</body>
</html>