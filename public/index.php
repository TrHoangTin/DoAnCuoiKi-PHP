<?php
session_start();
require_once '../app/models/ProductModel.php';
require_once '../app/helpers/SessionHelper.php';

$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

// Determine controller
$controllerName = isset($url[0]) && $url[0] != '' ? ucfirst($url[0]) . 'Controller' : 'DefaultController';

// Determine action
$action = isset($url[1]) && $url[1] != '' ? $url[1] : 'index';

// API routing
if ($controllerName === 'ApiController' && isset($url[1])) {
    $apiControllerName = ucfirst($url[1]) . 'ApiController';
    if (file_exists("../app/controllers/$apiControllerName.php")) {
        require_once "../app/controllers/$apiControllerName.php";
        $controller = new $apiControllerName();

        $method = $_SERVER['REQUEST_METHOD'];
        $id = $url[2] ?? null;

        switch ($method) {
            case 'GET': $action = $id ? 'show' : 'index'; break;
            case 'POST': $action = 'store'; break;
            case 'PUT': $action = 'update'; break;
            case 'DELETE': $action = 'destroy'; break;
            default:
                http_response_code(405);
                echo json_encode(['message' => 'Method Not Allowed']);
                exit;
        }

        if (method_exists($controller, $action)) {
            if ($id) call_user_func_array([$controller, $action], [$id]);
            else call_user_func_array([$controller, $action], []);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Action not found']);
        }
        exit;
    }
}

// Regular routing
if (file_exists("../app/controllers/$controllerName.php")) {
    require_once "../app/controllers/$controllerName.php";
    $controller = new $controllerName();
} else {
    die('Controller not found');
}

if (method_exists($controller, $action)) {
    call_user_func_array([$controller, $action], array_slice($url, 2));
} else {
    die('Action not found');
}