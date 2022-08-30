<?php
require __DIR__ . '/src/RiskController.php';

header('Content-type: application/json; charset=UTF-8');

$parts = explode('/', $_SERVER["REQUEST_URI"]);

if ($parts[2] !== 'risk') {
    http_response_code(404);
    exit;
}

$action = $parts[3] ?? null;
$controller = new RiskController();
$controller->processRequest($_SERVER['REQUEST_METHOD'], $action);