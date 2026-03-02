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

        $result = null;

        $totales = [
            "asistencias" => 0,
            "retardos" => 0,
            "puntuales" => 0,
            "fuera_de_rango" => 0,
            "horas" => 0
        ];

        if ($fecha_inicio && $fecha_fin) {

            $stmt = $this->conn->prepare("
                SELECT a.*, e.nombre AS empleado_nombre
                FROM asistencias a
                INNER JOIN empleados e ON a.empleado_id = e.id
                WHERE a.fecha BETWEEN ? AND ?
                ORDER BY a.fecha DESC
            ");

            $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
            $stmt->execute();
            $result = $stmt->get_result();

            // 🔹 Calcular totales
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

            // 🔹 Volver a ejecutar para mostrar la tabla
            $stmt->execute();
            $result = $stmt->get_result();
        }

        require_once "../app/views/reportes/index.php";
    }
}