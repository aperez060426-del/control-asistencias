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

    // 🔹 LISTADO UNA SOLA FILA POR EMPLEADO
    public function index() {

        $query = "
        SELECT 
        e.id,
        e.nombre,
        GROUP_CONCAT(
            CONCAT(
                UPPER(LEFT(h.dia_semana,1)), 
                LOWER(SUBSTRING(h.dia_semana,2)),
                ': ',
                TIME_FORMAT(h.hora_entrada,'%H:%i'),
                '-',
                TIME_FORMAT(h.hora_salida,'%H:%i')
            )
            ORDER BY FIELD(h.dia_semana,'lunes','martes','miercoles','jueves','viernes','sabado','domingo')
            SEPARATOR ' | '
        ) AS horario_semanal
        FROM empleados e
        LEFT JOIN horarios h ON e.id = h.empleado_id AND h.activo = 1
        WHERE e.activo = 1
        GROUP BY e.id
        ORDER BY e.nombre
        ";

        $result = $this->conn->query($query);

        if (!$result) {
            die("Error en consulta: " . $this->conn->error);
        }

        require_once "../app/views/horarios/index.php";
    }

    // 🔹 FORMULARIO SEMANAL
    public function crear() {

        $empleados = $this->conn->query("SELECT * FROM empleados WHERE activo = 1 ORDER BY nombre");

        if (!$empleados) {
            die("Error en consulta empleados: " . $this->conn->error);
        }

        require_once "../app/views/horarios/crear.php";
    }

    // 🔹 GUARDAR SEMANAL
    public function guardar() {

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $empleado_id = $_POST["empleado_id"];
            $entradas = $_POST["entrada"];
            $salidas = $_POST["salida"];

            foreach ($entradas as $dia => $hora_entrada) {

                $hora_salida = $salidas[$dia];

                if (empty($hora_entrada) || empty($hora_salida)) {
                    continue;
                }

                if (strtotime($hora_salida) <= strtotime($hora_entrada)) {
                    continue;
                }

                $horas = (strtotime($hora_salida) - strtotime($hora_entrada)) / 3600;

                $stmt = $this->conn->prepare("
                    INSERT INTO horarios
                    (empleado_id, dia_semana, hora_entrada, hora_salida, horas_totales, activo)
                    VALUES (?, ?, ?, ?, ?, 1)
                ");

                $stmt->bind_param("isssd", $empleado_id, $dia, $hora_entrada, $hora_salida, $horas);
                $stmt->execute();
            }

            header("Location: ?url=horarios");
            exit();
        }
    }
}