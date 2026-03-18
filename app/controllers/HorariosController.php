<?php

require_once "../config/Database.php";

class HorariosController {

    private $conn;

    public function __construct() {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION["usuario"])) {
            header("Location: /control-asistencias/public/");
            exit();
        }

        $db = new Database();
        $this->conn = $db->connect();
    }

    // ================================
    // 🔹 LISTADO DE HORARIOS
    // ================================
    public function index() {

         $query = "
          SELECT 
           e.id,
           e.nombre,
           h.dia_semana,
           h.hora_entrada,
           h.hora_salida,
           h.descanso
            FROM empleados e
            LEFT JOIN horarios h 
                ON e.id = h.empleado_id 
                AND h.activo = 1
            WHERE e.activo = 1
            ORDER BY e.nombre
        ";

    $result = $this->conn->query($query);

    if (!$result) {
        die("Error en consulta: " . $this->conn->error);
    }

    $empleados = [];

    while ($row = $result->fetch_assoc()) {

        $id = $row["id"];

        if (!isset($empleados[$id])) {
            $empleados[$id] = [
                "nombre" => $row["nombre"],
                "horarios" => []
            ];
        }

        if ($row["dia_semana"]) {
            $empleados[$id]["horarios"][$row["dia_semana"]] = $row;
        }
    }

    require_once "../app/views/horarios/index.php";
    }

    // ================================
// 🔹 LISTADO DE PLANTILLAS
// ================================
public function plantillas() {

    $plantillas = $this->conn->query("
        SELECT * FROM plantillas_horarios
        WHERE activo = 1
        ORDER BY nombre
    ");

    require_once "../app/views/horarios/plantillas.php";
}

    // ================================
    // 🔹 FORMULARIO ASIGNAR HORARIO
    // ================================
    public function crear() {

        $empleados = $this->conn->query("
            SELECT * FROM empleados 
            WHERE activo = 1 
            ORDER BY nombre
        ");

        $plantillas = $this->conn->query("
            SELECT * FROM plantillas_horarios 
            WHERE activo = 1
            ORDER BY nombre
        ");

        require_once "../app/views/horarios/crear.php";
    }

    // ================================
    // 🔹 GUARDAR HORARIO EMPLEADO
    // ================================
    

        public function guardar() {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {

                $empleado_id = intval($_POST["empleado_id"]);
                $entradas = $_POST["entrada"] ?? [];
                $salidas = $_POST["salida"] ?? [];
                $estados = $_POST["estado"] ?? [];

                // Desactivar horarios anteriores
                $stmtUpdate = $this->conn->prepare("
                    UPDATE horarios SET activo = 0 WHERE empleado_id = ?
                ");
                $stmtUpdate->bind_param("i", $empleado_id);
                $stmtUpdate->execute();

        $dias = ["lunes","martes","miercoles","jueves","viernes","sabado","domingo"];

        foreach ($dias as $dia) {

            $hora_entrada = $entradas[$dia] ?? null;
            $hora_salida = $salidas[$dia] ?? null;
            $estado = $estados[$dia] ?? "normal";

            // 🔹 Mapeo de estado a número
            $mapEstados = [
                "normal"     => 0,
                "descanso"   => 1,
                "vacaciones" => 2,
                "incapacidad"=> 3,
                "falta"      => 4
            ];
            $descanso = $mapEstados[$estado] ?? 0;

            if ($descanso > 0) {
                $stmt = $this->conn->prepare("
                    INSERT INTO horarios
                    (empleado_id, dia_semana, hora_entrada, hora_salida, horas_totales, descanso, activo)
                    VALUES (?, ?, NULL, NULL, NULL, ?, 1)
                ");
                $stmt->bind_param("isi", $empleado_id, $dia, $descanso);
                $stmt->execute();
            } elseif (!empty($hora_entrada) && !empty($hora_salida)) {
                $horaInicio = new DateTime($hora_entrada);
                $horaFin = new DateTime($hora_salida);
                $intervalo = $horaInicio->diff($horaFin);
                $horas_totales = $intervalo->h + ($intervalo->i / 60);

                $stmt = $this->conn->prepare("
                    INSERT INTO horarios
                    (empleado_id, dia_semana, hora_entrada, hora_salida, horas_totales, descanso, activo)
                    VALUES (?, ?, ?, ?, ?, 0, 1)
                ");
                $stmt->bind_param("isssd", $empleado_id, $dia, $hora_entrada, $hora_salida, $horas_totales);
                $stmt->execute();
            } else {
                $stmt = $this->conn->prepare("
                    INSERT INTO horarios
                    (empleado_id, dia_semana, hora_entrada, hora_salida, horas_totales, descanso, activo)
                    VALUES (?, ?, NULL, NULL, NULL, 0, 1)
                ");
                $stmt->bind_param("is", $empleado_id, $dia);
                $stmt->execute();
            }
        }

                header("Location: ?url=horarios");
                exit();
            }
        }





    // ================================
    // 🔹 FORMULARIO CREAR PLANTILLA
    // ================================
    public function crearPlantilla() {

        require_once "../app/views/horarios/crear_plantilla.php";
    }

    // ================================
    // 🔹 GUARDAR PLANTILLA
    // ================================
public function guardarPlantilla() {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nombre = trim($_POST["nombre"]);
        if (empty($nombre)) { die("Nombre requerido."); }

        // Guardamos la plantilla principal
        $stmt = $this->conn->prepare("
            INSERT INTO plantillas_horarios (nombre, activo)
            VALUES (?, 1)
        ");
        $stmt->bind_param("s", $nombre);
        $stmt->execute();

        $plantilla_id = $stmt->insert_id;

        $entradas = $_POST["entrada"] ?? [];
        $salidas = $_POST["salida"] ?? [];
        $estados = $_POST["estado"] ?? [];

        foreach ($entradas as $dia => $hora_entrada) {
            $hora_salida = $salidas[$dia] ?? null;
            $estado = isset($estados[$dia]) ? strtolower(trim($estados[$dia])) : "normal";

            // 🔹 Mapeo correcto
            $mapEstados = [
                "normal"     => 0,
                "descanso"   => 1,
                "vacaciones" => 2,
                "incapacidad"=> 3,
                "falta"      => 4
            ];
            $descanso = $mapEstados[$estado] ?? 0;

            // 🔹 Guardar detalle de la plantilla
            $stmt = $this->conn->prepare("
                INSERT INTO plantillas_horarios_detalle
                (plantilla_id, dia_semana, hora_entrada, hora_salida, descanso)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("isssi", $plantilla_id, $dia, $hora_entrada, $hora_salida, $descanso);
            $stmt->execute();
        }
    }
// <- ESTA LLAVE CIERRA BIEN LA FUNCIÓN
       header("Location: ?url=horarios/plantillas");
       exit();
   
}




    // ================================
    // 🔹 ELIMINAR PLANTILLA
    // ================================
    public function eliminarPlantilla() {

        $id = intval($_GET["id"]);

        if ($id <= 0) {
            header("Location: ?url=horarios/plantillas");
            exit();
        }

    // Eliminamos primero detalles
        $stmtDetalle = $this->conn->prepare("
            DELETE FROM plantillas_horarios_detalle
            WHERE plantilla_id = ?
       ");
       $stmtDetalle->bind_param("i", $id);
       $stmtDetalle->execute();

    // Luego plantilla principal
       $stmt = $this->conn->prepare("
           DELETE FROM plantillas_horarios
            WHERE id = ?
      ");
       $stmt->bind_param("i", $id);
       $stmt->execute();
    
       header("Location: ?url=horarios/plantillas");
       exit();
   }

    // ================================
// 🔹 FORMULARIO EDITAR PLANTILLA
// ================================
    public function editarPlantilla() {

        $id = intval($_GET["id"]);

        if ($id <= 0) {
            header("Location: ?url=horarios/plantillas");
            exit();
        }

    // Obtener plantilla principal
        $stmt = $this->conn->prepare("
            SELECT * FROM plantillas_horarios
            WHERE id = ?
      ");
       $stmt->bind_param("i", $id);
       $stmt->execute();
       $plantilla = $stmt->get_result()->fetch_assoc();

       if (!$plantilla) {
           header("Location: ?url=horarios/plantillas");
           exit();
        }

    // Obtener detalle
        $stmtDetalle = $this->conn->prepare("
            SELECT * FROM plantillas_horarios_detalle
            WHERE plantilla_id = ?
        ");
         $stmtDetalle->bind_param("i", $id);
         $stmtDetalle->execute();
         $resultDetalle = $stmtDetalle->get_result();

         $detalles = [];
         while ($row = $resultDetalle->fetch_assoc()) {
             $detalles[$row["dia_semana"]] = $row;
    }

    require_once "../app/views/horarios/editar_plantilla.php";
}
    
    // ================================
// 🔹 ACTUALIZAR PLANTILLA
// ================================
    public function actualizarPlantilla() {

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $id = intval($_POST["id"]);
            $nombre = trim($_POST["nombre"]);

            if ($id <= 0 || empty($nombre)) {
                header("Location: ?url=horarios/plantillas");
                exit();
            }

            // Actualizar nombre
            $stmt = $this->conn->prepare("
                UPDATE plantillas_horarios
                SET nombre = ?
                WHERE id = ?
            ");
            $stmt->bind_param("si", $nombre, $id);
            $stmt->execute();

            // Borrar detalles anteriores
            $stmtDelete = $this->conn->prepare("
                DELETE FROM plantillas_horarios_detalle
                WHERE plantilla_id = ?
            ");
            $stmtDelete->bind_param("i", $id);
            $stmtDelete->execute();

            $entradas = $_POST["entrada"] ?? [];
            $salidas = $_POST["salida"] ?? [];
            $estados = $_POST["estado"] ?? [];

            foreach ($entradas as $dia => $hora_entrada) {
                $hora_salida = $salidas[$dia] ?? null;
                $estado = $estados[$dia] ?? "normal";

                // Mapear estado a número
                $descanso = 0;
                if ($estado === "descanso") $descanso = 1;
                elseif ($estado === "vacaciones") $descanso = 2;
                elseif ($estado === "incapacidad") $descanso = 3;
                elseif ($estado === "falta") $descanso = 4;

                $stmtDetalle = $this->conn->prepare("
                    INSERT INTO plantillas_horarios_detalle
                    (plantilla_id, dia_semana, hora_entrada, hora_salida, descanso)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmtDetalle->bind_param("isssi", $id, $dia, $hora_entrada, $hora_salida, $descanso);
                $stmtDetalle->execute();
            }

            header("Location: ?url=horarios/plantillas");
            exit();
        }
    }

        // ================================
// 🔹 Editar Plantilla Para Empleado
// ================================
public function editar() {
    $id = intval($_GET["id"]);
    if ($id <= 0) header("Location: ?url=horarios");

    // Obtener empleado
    $stmtEmp = $this->conn->prepare("SELECT * FROM empleados WHERE id = ?");
    $stmtEmp->bind_param("i", $id);
    $stmtEmp->execute();
    $empleado = $stmtEmp->get_result()->fetch_assoc();

    // Obtener horarios activos
    $stmt = $this->conn->prepare("SELECT * FROM horarios WHERE empleado_id = ? AND activo = 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    $horarios = [];
    while($row = $result->fetch_assoc()) {
        $horarios[$row["dia_semana"]] = $row;
    }

    // Obtener plantillas
    $plantillas = $this->conn->query("
        SELECT * FROM plantillas_horarios
        WHERE activo = 1
        ORDER BY nombre
    ");

    require_once "../app/views/horarios/editar.php";
}



    // ================================
    // 🔹 OBTENER PLANTILLA (AJAX)
    // ================================
    public function obtenerPlantilla() {

        $id = intval($_GET["id"]);

        $stmt = $this->conn->prepare("
            SELECT dia_semana, hora_entrada, hora_salida, descanso
            FROM plantillas_horarios_detalle
            WHERE plantilla_id = ?
        ");

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        $datos = [];

        while ($row = $result->fetch_assoc()) {
            $datos[] = $row;
        }

        echo json_encode($datos);
        exit();
    }

}