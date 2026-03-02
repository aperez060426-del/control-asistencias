<?php

session_start();

date_default_timezone_set('America/Mexico_City');

require_once "../app/core/Router.php";

$router = new Router();
$router->route();