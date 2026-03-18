<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Login - Control Asistencias</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    margin:0;
    height:100vh;
    display:flex;
    flex-direction:column;       /* Apila elementos verticalmente */
    justify-content:center;      /* Centra verticalmente todo el contenido */
    align-items:center;          /* Centra horizontalmente */
    background:linear-gradient(135deg, #1e3c72, #2a5298);
    font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;
}

.header {
    text-align: center;
    margin-bottom: 20px;         /* Espacio entre el logo y la caja de login */
}

.header img {
    width: 240px;
}

.login-box{
    background:white;
    padding:40px;
    width:350px;
    border-radius:20px;
    box-shadow:0 20px 40px rgba(0,0,0,0.2);
    text-align:center;
}

.login-box h2{
    margin-bottom:30px;
    font-weight:600;
    color:#1f2937;
}

.form-control{
    border-radius:12px;
    padding:12px;
}

.btn-login{
    width:100%;
    padding:12px;
    border-radius:50px;
    background:#2563eb;
    border:none;
    font-weight:600;
    transition:0.3s;
    color:white;
}

.btn-login:hover{
    background:#1d4ed8;
    transform:translateY(-3px);
}
</style>
</head>

<body>
    <div class="header">
        <img src="imagen/Grupo-Plaza-2024-blanca.png" alt="Logo Grupo Plaza">
    </div>

    <div class="login-box">
        <h2>Login Sistema</h2>
        <form method="POST" action="?url=auth/login">
            <div class="mb-3">
                <input type="text" name="codigo" class="form-control" placeholder="Código de empleado" required>
            </div>

            <div class="mb-4">
                <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
            </div>

            <button type="submit" class="btn btn-login">Ingresar</button>
        </form>
    </div>
</body>
</html>