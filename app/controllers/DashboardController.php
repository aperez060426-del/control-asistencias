<?php

require_once "../config/Database.php";

class DashboardController {

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

        // 👤 Total empleados activos
        $empleados = $this->conn->query("SELECT COUNT(*) as total FROM empleados WHERE activo = 1");
        $total_empleados = $empleados->fetch_assoc()["total"];

        // 🏢 Total sucursales
        // 🏢 Total sucursales
        $sucursales = $this->conn->query("SELECT COUNT(*) as total FROM sucursales");
        $total_sucursales = $sucursales->fetch_assoc()["total"];

        // 📅 Fecha actual
        $hoy = date("Y-m-d");

        // 📋 Asistencias hoy
        $asistencias = $this->conn->query("SELECT * FROM asistencias WHERE fecha = '$hoy'");
        $total_asistencias = $asistencias->num_rows;

        $total_retardos = 0;
        $total_horas = 0;

        while ($row = $asistencias->fetch_assoc()) {

            if ($row["estado"] == "retardo") {
                $total_retardos++;
            }

            if (!empty($row["hora_salida"])) {
                $entrada = strtotime($row["hora_entrada"]);
                $salida = strtotime($row["hora_salida"]);
                $horas = ($salida - $entrada) / 3600;
                $total_horas += $horas;
            }
        }

        require_once "../app/views/dashboard.php";
    }
}