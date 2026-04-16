<?php

require_once "../config/Database.php";

class ChecadorController {

    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function index() {
        require_once "../app/views/checador/index.php";
    }
    

    public function registrar() {

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $codigo = strtoupper(trim($_POST["codigo"]));
            $tipo = $_POST["tipo"] ?? "";

            $lat = $_POST["latitud"] ?? null;
            $lng = $_POST["longitud"] ?? null;

            if (!$lat || !$lng) {
                $this->mensaje("Ubicación no detectada", "error");
            }

            // 🔎 Buscar empleado
            $stmt = $this->conn->prepare("
                SELECT * FROM empleados 
                WHERE codigo = ?
                AND activo = 1 
            ");
            $stmt->bind_param("s", $codigo);
            $stmt->execute();
            $empleado = $stmt->get_result()->fetch_assoc();

            if (!$empleado) {
                $this->mensaje("Empleado no encontrado", "error");
            }

            // 🔎 Obtener sucursal
            $stmt2 = $this->conn->prepare("
                SELECT * FROM sucursales 
                WHERE id = ? AND activa = 1
            ");
            $stmt2->bind_param("i", $empleado["sucursal_id"]);
            $stmt2->execute();
            $sucursal = $stmt2->get_result()->fetch_assoc();

            if (!$sucursal) {
                $this->mensaje("Sucursal no válida", "error");
            }

            // 📍 VALIDAR DISTANCIA
            $latSucursal = $sucursal["latitud"];
            $lngSucursal = $sucursal["longitud"];
            $radioPermitido = 100; // metros

            $distancia = $this->calcularDistancia($lat, $lng, $latSucursal, $lngSucursal);

            if ($distancia > $radioPermitido) {
                $this->mensaje("Fuera del rango permitido", "error");
            }

            $fecha = date("Y-m-d");
            $hora_actual = date("Y-m-d H:i:s");

            // 🔎 Buscar asistencia activa
            $check = $this->conn->prepare("
                SELECT * FROM asistencias
                WHERE empleado_id = ?
                AND fecha = ?
                AND hora_salida IS NULL
            ");
            $check->bind_param("is", $empleado["id"], $fecha);
            $check->execute();
            $asistencia = $check->get_result()->fetch_assoc();

            // 🚫 VALIDACIONES
            if ($asistencia && $tipo == "entrada") {
                $this->mensaje("Ya registraste entrada", "error");
            }

            if (!$asistencia && $tipo == "salida") {
                $this->mensaje("Primero registra entrada", "error");
            }

            // =========================
            // 🔥 ENTRADA
            // =========================
            if ($tipo == "entrada" && !$asistencia) {

                $stmtHorario = $this->conn->prepare("
                    SELECT * FROM horarios WHERE empleado_id = ?
                ");
                $stmtHorario->bind_param("i", $empleado["id"]);
                $stmtHorario->execute();
                $horario = $stmtHorario->get_result()->fetch_assoc();

                $estado = "puntual";

                if ($horario) {
                    $horaEntradaProgramada = strtotime($fecha . " " . $horario["hora_entrada"]);
                    $horaActualTime = strtotime($hora_actual);
                    $tolerancia = $horario["tolerancia_minutos"] * 60;

                    if ($horaActualTime > ($horaEntradaProgramada + $tolerancia)) {
                        $estado = "retardo";
                    }
                }

                $stmtInsert = $this->conn->prepare("
                    INSERT INTO asistencias 
                    (empleado_id, sucursal_id, fecha, hora_entrada, latitud, longitud, estado)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");

                $stmtInsert->bind_param(
                    "iissdds",
                    $empleado["id"],
                    $sucursal["id"],
                    $fecha,
                    $hora_actual,
                    $lat,
                    $lng,
                    $estado
                );

                $stmtInsert->execute();

                $this->mensaje("Entrada registrada", "success");
            }

            // =========================
            // 🔥 SALIDA
            // =========================
            if ($tipo == "salida" && $asistencia) {

                $stmtHorario = $this->conn->prepare("
                    SELECT * FROM horarios WHERE empleado_id = ?
                ");
                $stmtHorario->bind_param("i", $empleado["id"]);
                $stmtHorario->execute();
                $horario = $stmtHorario->get_result()->fetch_assoc();

                if ($horario) {
                    $horaSalidaProgramada = strtotime($fecha . " " . $horario["hora_salida"]);
                    $horaActualTime = strtotime($hora_actual);

                    if ($horaActualTime < $horaSalidaProgramada && !isset($_POST["confirmar"])) {

                        echo '
                        <div style="text-align:center; font-family:Arial;">
                            <h2 style="color:orange;">⚠️ Salida anticipada</h2>
                            <form method="POST" action="?url=checador/registrar">
                                <input type="hidden" name="codigo" value="'.$codigo.'">
                                <input type="hidden" name="tipo" value="salida">
                                <input type="hidden" name="latitud" value="'.$lat.'">
                                <input type="hidden" name="longitud" value="'.$lng.'">
                                <input type="hidden" name="confirmar" value="1">
                                <button type="submit" style="padding:15px;">
                                    Confirmar salida
                                </button>
                            </form>
                        </div>
                        ';
                        exit();
                    }
                }

                $stmtUpdate = $this->conn->prepare("
                    UPDATE asistencias 
                    SET hora_salida = ?
                    WHERE id = ?
                ");

                $stmtUpdate->bind_param(
                    "si",
                    $hora_actual,
                    $asistencia["id"]
                );

                $stmtUpdate->execute();

                $this->mensaje("Salida registrada", "success");
            }
        }
    }

    private function mensaje($texto, $tipo) {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION["flash_mensaje"] = $texto;
        $_SESSION["flash_tipo"] = $tipo;

        header("Location: ?url=checador");
        exit();
    }

    private function calcularDistancia($lat1, $lon1, $lat2, $lon2) {

        $radio_tierra = 6371000;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $radio_tierra * $c;
    }
}