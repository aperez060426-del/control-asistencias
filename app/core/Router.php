<?php

class Router {

    public function route() {

        if (!isset($_GET['url'])) {
            require_once "../app/views/login.php";
            return;
        }

        $url = explode("/", $_GET['url']);

        $controllerName = ucfirst($url[0]) . "Controller";
        $method = isset($url[1]) ? $url[1] : "index";

        $controllerFile = "../app/controllers/" . $controllerName . ".php";

        if (file_exists($controllerFile)) {

            require_once $controllerFile;

            $controller = new $controllerName();

            // 🔥 AQUI ESTÁ LA CLAVE
            $params = array_slice($url, 2);

            if (method_exists($controller, $method)) {

                call_user_func_array([$controller, $method], $params);

            } else {
                echo "Método no encontrado";
            }

        } else {
            echo "Controlador no encontrado";
        }
    }
}