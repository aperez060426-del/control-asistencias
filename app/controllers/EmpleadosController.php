<?php

require_once "../config/Database.php";

class EmpleadosController {

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
                SELECT e.*, s.nombre AS sucursal_nombre
                FROM empleados e
                INNER JOIN sucursales s ON e.sucursal_id = s.id
                WHERE e.activo = 1
            ";

        $result = $this->conn->query($query);

        require_once "../app/views/empleados/index.php";
    }

    public function crear() {

        $sucursales = $this->conn->query("SELECT * FROM sucursales WHERE activa = 1");

        require_once "../app/views/empleados/crear.php";
    }

    public function guardar() {

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $codigo = $_POST["codigo"];
            $nombre = $_POST["nombre"];
            $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
            $rol = $_POST["rol"];
            $sucursal_id = $_POST["sucursal_id"];

            $stmt = $this->conn->prepare("
                INSERT INTO empleados
                (empresa_id, sucursal_id, codigo, nombre, password, rol, activo)
                VALUES (1, ?, ?, ?, ?, ?, 1)
            ");

            $stmt->bind_param("issss", $sucursal_id, $codigo, $nombre, $password, $rol);
            $stmt->execute();

            header("Location: ?url=empleados");
            exit();
        }
    }

    // 🔹 NUEVO — Editar empleado
    public function editar($id) {

        $stmt = $this->conn->prepare("SELECT * FROM empleados WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $empleado = $stmt->get_result()->fetch_assoc();

        $sucursales = $this->conn->query("SELECT * FROM sucursales WHERE activa = 1");

        require_once "../app/views/empleados/editar.php";
    }


    public function eliminar($id) {

    $stmt = $this->conn->prepare("
        UPDATE empleados SET activo = 0 WHERE id = ?
    ");

    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: ?url=empleados");
    exit();


    
}

    // 🔹 NUEVO — Actualizar empleado
    public function actualizar() {

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $id = $_POST["id"];
            $codigo = $_POST["codigo"];
            $nombre = $_POST["nombre"];
            $rol = $_POST["rol"];
            $sucursal_id = $_POST["sucursal_id"];
            $activo = $_POST["activo"];
            $password = $_POST["password"];

            // Si escribe nueva contraseña
            if (!empty($password)) {

                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $this->conn->prepare("
                    UPDATE empleados 
                    SET codigo=?, nombre=?, password=?, rol=?, sucursal_id=?, activo=?
                    WHERE id=?
                ");

                $stmt->bind_param("ssssiii", $codigo, $nombre, $passwordHash, $rol, $sucursal_id, $activo, $id);

            } else {

                $stmt = $this->conn->prepare("
                    UPDATE empleados 
                    SET codigo=?, nombre=?, rol=?, sucursal_id=?, activo=?
                    WHERE id=?
                ");

                        $stmt->bind_param("sssiii", $codigo, $nombre, $rol, $sucursal_id, $activo, $id);            }

            $stmt->execute();

            header("Location: ?url=empleados");
            exit();
        }
    }
}