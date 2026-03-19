<?php

require_once "../config/Database.php";

class SucursalesController {

    private $conn;

    public function __construct() {
        if (!isset($_SESSION["usuario"])) {
            header("Location: /control-asistencias/public/");
            exit();
        }

        $db = new Database();
        $this->conn = $db->connect();
    }

    public function index() {

        $query = "SELECT * FROM sucursales WHERE activa = 1";
        $result = $this->conn->query($query);

        require_once "../app/views/sucursales/index.php";
    }

    public function crear() {

        // 🔒 BLOQUEO
        if ($_SESSION["usuario"]["rol"] == "supervisor_marca") {
            echo "<script>
            alert('No tienes permisos para crear sucursales');
            window.location.href='?url=sucursales';
            </script>";
            exit();
        }

        require_once "../app/views/sucursales/crear.php";
    }

    public function guardar() {

        // 🔒 BLOQUEO
        if ($_SESSION["usuario"]["rol"] == "supervisor_marca") {
            exit("Acceso denegado");
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $marca = $_POST["marca"];       
            $nombre = $_POST["nombre"];
            $direccion = $_POST["direccion"];
            $latitud = $_POST["latitud"];
            $longitud = $_POST["longitud"];
            $radio = $_POST["radio"];
            $tolerancia = $_POST["tolerancia"];

            $stmt = $this->conn->prepare("
                INSERT INTO sucursales 
                (empresa_id, marca, nombre, direccion, latitud, longitud, radio_metros, tolerancia_minutos)
                VALUES (1, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->bind_param("sssddii", $marca, $nombre, $direccion, $latitud, $longitud, $radio, $tolerancia);
            $stmt->execute();

            header("Location: ?url=sucursales");
            exit();
        }
    }

    /* ===============================
       EDITAR SUCURSAL
    =============================== */

    public function editar(){

        // 🔒 BLOQUEO
        if ($_SESSION["usuario"]["rol"] == "supervisor_marca") {
            echo "<script>
            alert('No tienes permisos para editar sucursales');
            window.location.href='?url=sucursales';
            </script>";
            exit();
        }

        $id = $_GET["id"];

        $stmt = $this->conn->prepare("SELECT * FROM sucursales WHERE id = ?");
        $stmt->bind_param("i",$id);
        $stmt->execute();

        $result = $stmt->get_result();
        $sucursal = $result->fetch_assoc();

        require_once "../app/views/sucursales/editar.php";
    }

    /* ===============================
       ACTUALIZAR SUCURSAL
    =============================== */

    public function actualizar(){

        // 🔒 BLOQUEO
        if ($_SESSION["usuario"]["rol"] == "supervisor_marca") {
            exit("Acceso denegado");
        }

        if($_SERVER["REQUEST_METHOD"] == "POST"){

            $id = $_POST["id"];
            $nombre = $_POST["nombre"];
            $direccion = $_POST["direccion"];
            $latitud = $_POST["latitud"];
            $longitud = $_POST["longitud"];
            $radio = $_POST["radio"];
            $tolerancia = $_POST["tolerancia"];

            $stmt = $this->conn->prepare("
                UPDATE sucursales 
                SET nombre=?, direccion=?, latitud=?, longitud=?, radio_metros=?, tolerancia_minutos=?
                WHERE id=?
            ");

            $stmt->bind_param("ssddiii",$nombre,$direccion,$latitud,$longitud,$radio,$tolerancia,$id);
            $stmt->execute();

            header("Location: ?url=sucursales");
            exit();
        }
    }

    /* ===============================
       ELIMINAR SUCURSAL
    =============================== */

    public function eliminar(){

        // 🔒 SOLO ADMIN (igual que empleados)
        if ($_SESSION["usuario"]["rol"] != "admin") {
            echo "<script>
            alert('No tienes permisos para eliminar sucursales');
            window.location.href='?url=sucursales';
            </script>";
            exit();
        }

        $id = $_GET["id"];

        $stmt = $this->conn->prepare("DELETE FROM sucursales WHERE id = ?");
        $stmt->bind_param("i",$id);
        $stmt->execute();

        header("Location: ?url=sucursales");
        exit();
    }

}