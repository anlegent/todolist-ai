<?php
// index.php - Point d'entrée de l'application

require_once 'config/Database.php';
require_once 'models/Todo.php';
require_once 'controllers/TodoController.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Gestion de la requête OPTIONS pour CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$controller = new TodoController();

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/index.php', '', $path);

// Router simple
if (preg_match('/^\/api\/todos\/(\d+)$/', $path, $matches)) {
    $id = $matches[1];
    switch ($method) {
        case 'GET':
            $controller->read($id);
            break;
        case 'PUT':
            $controller->update($id);
            break;
        case 'DELETE':
            $controller->delete($id);
            break;
    }
} elseif ($path === '/api/todos' || $path === '/api/todos/') {
    switch ($method) {
        case 'GET':
            $controller->readAll();
            break;
        case 'POST':
            $controller->create();
            break;
    }
} else {
    http_response_code(404);
    echo json_encode(['message' => 'Route not found']);
}
