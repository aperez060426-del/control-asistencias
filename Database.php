<?php

class Database {

    private $host = "AQUI_TU_ENDPOINT_AWS";
    private $dbname = "AQUI_TU_BASE";
    private $username = "AQUI_TU_USUARIO";
    private $password = "AQUI_TU_PASSWORD";

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