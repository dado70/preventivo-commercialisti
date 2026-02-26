<?php
/**
 * Preventivo Commercialisti - Router
 * Copyright (C) 2024 Alessandro Scapuzzi <dado70@gmail.com>
 * Licenza: GPL v3
 */

namespace App\Core;

class Router
{
    private array $routes = [];

    public function add(string $method, string $pattern, array $handler): void
    {
        $this->routes[] = [
            'method'  => strtoupper($method),
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }

    public function get(string $pattern, array $handler): void
    {
        $this->add('GET', $pattern, $handler);
    }

    public function post(string $pattern, array $handler): void
    {
        $this->add('POST', $pattern, $handler);
    }

    public function dispatch(string $uri, string $method): void
    {
        // Rimuove query string
        $path = parse_url($uri, PHP_URL_PATH);

        // Rimuove il base path dall'URI
        $basePath = parse_url(BASE_URL, PHP_URL_PATH);
        if ($basePath && str_starts_with($path, $basePath)) {
            $path = substr($path, strlen($basePath));
        }
        $path = '/' . ltrim($path, '/');

        foreach ($this->routes as $route) {
            if ($route['method'] !== strtoupper($method)) continue;

            $params = [];
            $regex = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $route['pattern']);
            $regex = '#^' . $regex . '$#';

            if (preg_match($regex, $path, $matches)) {
                foreach ($matches as $key => $value) {
                    if (is_string($key)) $params[$key] = $value;
                }

                [$controllerClass, $method] = $route['handler'];
                $controller = new $controllerClass();
                $controller->$method($params);
                return;
            }
        }

        // 404
        http_response_code(404);
        include SRC_PATH . '/Views/errors/404.php';
    }
}
