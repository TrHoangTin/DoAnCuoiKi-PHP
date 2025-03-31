<?php
class Router {
    public static function route($url, $controllerMethod) {
        $request = $_SERVER['REQUEST_URI'];
        $route = str_replace('/webbanhang', '', $request);
        $route = explode('?', $route)[0];

        if ($route === $url) {
            $parts = explode('@', $controllerMethod);
            $controller = $parts[0];
            $method = $parts[1];

            require_once __DIR__ . '/../controllers/' . $controller . '.php';
            $controller = new $controller();
            $controller->$method();
            exit();
        }
    }
}