<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/src/Router.php';

$router = new Router();
$router->dispatch();
