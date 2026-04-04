<?php
// src/Core/Router.php

namespace App\Core;

class Router {
    private $routes = [];

    public function add($method, $path, $handler) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function dispatch($requestUri, $requestMethod) {
        $url = parse_url($requestUri, PHP_URL_PATH);
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && $route['path'] === $url) {
                return $this->callHandler($route['handler']);
            }
        }

        header("HTTP/1.0 404 Not Found");
        echo "404 Not Found";
    }

    private function callHandler($handler) {
        if (is_callable($handler)) {
            return call_user_func($handler);
        }
        
        if (is_string($handler) && strpos($handler, '@') !== false) {
            list($controller, $method) = explode('@', $handler);
            $controller = "App\\Controllers\\" . $controller;
            if (class_exists($controller)) {
                $instance = new $controller();
                if (method_exists($instance, $method)) {
                    return $instance->$method();
                }
            }
        }
        
        header("HTTP/1.0 500 Internal Server Error");
        echo "Invalid handler";
    }
}
