<?php

require_once "../config/Database.php";

class AuthController {

    private $conn;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $db = new Database();
        $this->conn = $db->connect();
    }

    public function index() {
        require_once "../app/views/login.php";
    }

    public function login() {

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $codigo = $_POST["codigo"];
            $password = $_POST["password"];

            $query = "SELECT * FROM empleados WHERE codigo = ? AND activo = 1";
            $stmt = $this->conn->prepare($query);

            if (!$stmt) {
                die("Error en prepare: " . $this->conn->error);
            }

            $stmt->bind_param("s", $codigo);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {

                $usuario = $result->fetch_assoc();

                if (password_verify($password, $usuario["password"])) {

                    // 🔥 Guardamos el usuario COMPLETO
                    $_SESSION["usuario"] = $usuario;

                    // Opcional (si quieres seguir usando estos accesos directos)
                    $_SESSION["rol"] = $usuario["rol"];
                    $_SESSION["empleado_id"] = $usuario["id"];

                    header("Location: ?url=dashboard");
                    exit();

                } else {
                    echo "Contraseña incorrecta";
                }

            } else {
                echo "Empleado no encontrado o inactivo";
            }
        }
    }

    public function logout() {

        session_start();
        session_unset();
        session_destroy();

        header("Location: /control-asistencias/public/");
        exit();
    }
}