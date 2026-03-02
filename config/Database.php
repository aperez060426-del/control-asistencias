<?php

class Database {

    private $host = "qa-sistemas2026.cv4ukg8eud10.us-east-2.rds.amazonaws.com";
    private $dbname = "control_asistencias";
    private $username = "admin";
    private $password = "sistemas2026";

    public function connect() {

        $connection = new mysqli(
            $this->host,
            $this->username,
            $this->password,
            $this->dbname
        );

        if ($connection->connect_error) {
            die("Error de conexión: " . $connection->connect_error);
        }

        return $connection;
    }
}