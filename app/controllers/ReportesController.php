<?php

require_once "../config/Database.php";

class ReportesController {

    private $conn;

    public function __construct() {

        // ✅ Iniciar sesión si no está iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // ✅ Verificar que haya usuario logueado
        if (!isset($_SESSION["usuario"])) {
            header("Location: /control-asistencias/public/");
            exit();
        }

        // 🔒 Solo ADMIN (tu base de datos usa "admin")
        if (!isset($_SESSION["usuario"]["rol"]) || 
            strtolower($_SESSION["usuario"]["rol"]) != "admin") {

            die("Acceso denegado.");
        }

        $db = new Database();
        $this->conn = $db->connect();
    }

  public function index() {

    $fecha_inicio = $_GET["fecha_inicio"] ?? null;
    $fecha_fin = $_GET["fecha_fin"] ?? null;

    // ✅ NUEVOS FILTROS
    $sucursal = $_GET["sucursal"] ?? null;
    $empleado = $_GET["empleado"] ?? null;
    $filtro_estado = $_GET["filtro_estado"] ?? null;

    // ✅ OBTENER SUCURSALES (para el select)
    $sucursales = $this->conn->query("SELECT id, nombre FROM sucursales");

    $result = null;

    $totales = [
        "asistencias" => 0,
        "retardos" => 0,
        "puntuales" => 0,
        "fuera_de_rango" => 0,
        "horas" => 0
    ];

    if ($fecha_inicio && $fecha_fin) {

        // ✅ QUERY DINÁMICO
        $query = "
            SELECT a.*, e.nombre AS empleado_nombre
            FROM asistencias a
            INNER JOIN empleados e ON a.empleado_id = e.id
            WHERE a.fecha BETWEEN ? AND ?
        ";

        $params = [$fecha_inicio, $fecha_fin];
        $types = "ss";

        // 🔹 FILTRO SUCURSAL
        if (!empty($sucursal)) {
            $query .= " AND e.sucursal_id = ?";
            $params[] = $sucursal;
            $types .= "i";
        }

        // 🔹 FILTRO EMPLEADO
        if (!empty($empleado)) {
            $query .= " AND e.nombre LIKE ?";
            $params[] = "%$empleado%";
            $types .= "s";
        }

        // 🔹 FILTRO ESTADO
        if (!empty($filtro_estado)) {

            if ($filtro_estado == "falta" || $filtro_estado == "fuera_de_rango") {
                $query .= " AND (a.estado = 'falta' OR a.estado = 'fuera_de_rango')";
            } 
            elseif ($filtro_estado == "incapacidad" || $filtro_estado == "descanso") {
                $query .= " AND (a.estado = 'incapacidad' OR a.estado = 'descanso')";
            } 
            else {
                $query .= " AND a.estado = ?";
                $params[] = $filtro_estado;
                $types .= "s";
            }
        }

        $query .= " ORDER BY a.fecha DESC";

        // ✅ PREPARE DINÁMICO
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        // 🔹 TOTALES (tu lógica intacta)
        while ($row = $result->fetch_assoc()) {

            $totales["asistencias"]++;

            if ($row["estado"] == "retardo") {
                $totales["retardos"]++;
            }

            if ($row["estado"] == "puntual") {
                $totales["puntuales"]++;
            }

            if ($row["estado"] == "fuera_de_rango") {
                $totales["fuera_de_rango"]++;
            }

            if (!empty($row["hora_salida"])) {
                $entrada = strtotime($row["hora_entrada"]);
                $salida = strtotime($row["hora_salida"]);
                $horas = ($salida - $entrada) / 3600;
                $totales["horas"] += $horas;
            }
        }

        // 🔁 RE-EJECUTAR PARA LA TABLA
        $stmt->execute();
        $result = $stmt->get_result();
    }

    require_once "../app/views/reportes/index.php";
}
}