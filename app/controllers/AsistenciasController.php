<?php

require_once "../config/Database.php";

class AsistenciasController {

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

    public function index() {

        $query = "
            SELECT a.*, e.nombre AS empleado_nombre, s.nombre AS sucursal_nombre
            FROM asistencias a
            INNER JOIN empleados e ON a.empleado_id = e.id
            INNER JOIN sucursales s ON a.sucursal_id = s.id
            ORDER BY a.fecha DESC, a.hora_entrada DESC
        ";

        $result = $this->conn->query($query);
   
        $sucursales = $this->conn->query("SELECT * FROM sucursales WHERE activa = 1");

        require_once "../app/views/asistencias/index.php";
    }

    public function registrar() {

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $empleado_id = $_POST["empleado_id"];
            $tipo = $_POST["tipo"];
            $lat_usuario = $_POST["latitud"] ?? null;
            $lng_usuario = $_POST["longitud"] ?? null;

            if (!$lat_usuario || !$lng_usuario) {
                die("No se pudo obtener la ubicación. Activa el GPS.");
            }

            $fecha = date("Y-m-d");
            $hora_actual = date("Y-m-d H:i:s");

            // 🔹 Obtener sucursal del empleado + datos GPS
            $stmtSucursal = $this->conn->prepare("
                SELECT s.id, s.latitud, s.longitud, s.radio_metros, s.tolerancia_minutos
                FROM empleados e
                INNER JOIN sucursales s ON e.sucursal_id = s.id
                WHERE e.id = ?
            ");
            $stmtSucursal->bind_param("i", $empleado_id);
            $stmtSucursal->execute();
            $resultado = $stmtSucursal->get_result();
            $sucursal = $resultado->fetch_assoc();

            if (!$sucursal) {
                die("Error: Empleado sin sucursal válida.");
            }

            $sucursal_id = $sucursal["id"];

            // ================================
            // 📍 VALIDAR DISTANCIA (HAVERSINE)
            // ================================
            function calcularDistancia($lat1, $lon1, $lat2, $lon2) {
                $radio_tierra = 6371000; // metros

                $dLat = deg2rad($lat2 - $lat1);
                $dLon = deg2rad($lon2 - $lon1);

                $a = sin($dLat/2) * sin($dLat/2) +
                     cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
                     sin($dLon/2) * sin($dLon/2);

                $c = 2 * atan2(sqrt($a), sqrt(1-$a));
                return $radio_tierra * $c;
            }

            $distancia = calcularDistancia(
                $lat_usuario,
                $lng_usuario,
                $sucursal["latitud"],
                $sucursal["longitud"]
            );

            if ($distancia > $sucursal["radio_metros"]) {
                die("No estás dentro del rango permitido de la sucursal.");
            }

            // ====================================================
            // 🔥 REGISTRAR ENTRADA
            // ====================================================
            if ($tipo == "entrada") {

                $dia_actual = strtolower(date("l"));

                $dias = [
                    "monday" => "lunes",
                    "tuesday" => "martes",
                    "wednesday" => "miercoles",
                    "thursday" => "jueves",
                    "friday" => "viernes",
                    "saturday" => "sabado",
                    "sunday" => "domingo"
                ];

                $dia_semana = $dias[$dia_actual];

                $stmtHorario = $this->conn->prepare("
                    SELECT hora_entrada 
                    FROM horarios 
                    WHERE empleado_id = ? 
                    AND dia_semana = ?
                    LIMIT 1
                ");

                $stmtHorario->bind_param("is", $empleado_id, $dia_semana);
                $stmtHorario->execute();
                $resultadoHorario = $stmtHorario->get_result();
                $horario = $resultadoHorario->fetch_assoc();

                $estado = "puntual";

                if (!$horario) {
                    $estado = "fuera_de_rango";
                } else {

                    $hora_programada = strtotime(date("Y-m-d") . " " . $horario["hora_entrada"]);
                    $hora_real = strtotime($hora_actual);

                    $diferencia_minutos = ($hora_real - $hora_programada) / 60;

                    if ($diferencia_minutos > 0) {
                        $estado = "r
                        
                        
                        etardo";
                    } else {
                        $estado = "puntual";
                    }
                }

                $stmt = $this->conn->prepare("
                    INSERT INTO asistencias 
                    (empleado_id, sucursal_id, fecha, hora_entrada, estado)
                    VALUES (?, ?, ?, ?, ?)
                ");

                $stmt->bind_param("iisss", $empleado_id, $sucursal_id, $fecha, $hora_actual, $estado);
                $stmt->execute();

            }
            // ====================================================
            // 🔥 REGISTRAR SALIDA
            // ====================================================
            else {

                $stmt = $this->conn->prepare("
                    UPDATE asistencias 
                    SET hora_salida = ?
                    WHERE empleado_id = ? 
                    AND fecha = ? 
                    AND hora_salida IS NULL
                ");

                $stmt->bind_param("sis", $hora_actual, $empleado_id, $fecha);
                $stmt->execute();
            }

            header("Location: ?url=asistencias");
            exit();
        }
    }
}