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
        require_once "../app/views/sucursales/crear.php";
    }

    public function guardar() {

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $nombre = $_POST["nombre"];
            $direccion = $_POST["direccion"];
            $latitud = $_POST["latitud"];
            $longitud = $_POST["longitud"];
            $radio = $_POST["radio"];
            $tolerancia = $_POST["tolerancia"];

            $stmt = $this->conn->prepare("
                INSERT INTO sucursales 
                (empresa_id, nombre, direccion, latitud, longitud, radio_metros, tolerancia_minutos)
                VALUES (1, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->bind_param("ssddii", $nombre, $direccion, $latitud, $longitud, $radio, $tolerancia);
            $stmt->execute();

            header("Location: ?url=sucursales");
            exit();
        }
    }
}