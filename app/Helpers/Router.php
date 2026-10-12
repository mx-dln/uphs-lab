<?php

namespace App\Helpers;

class Router
{
    private static array $routes = [];
    private static array $middleware = [];
    private static array $groupMiddleware = [];
    private static string $prefix = '';

    public static function get(string $path, $handler, array $middleware = []): void
    {
        self::addRoute('GET', $path, $handler, $middleware);
    }

    public static function post(string $path, $handler, array $middleware = []): void
    {
        self::addRoute('POST', $path, $handler, $middleware);
    }

    public static function put(string $path, $handler, array $middleware = []): void
    {
        self::addRoute('PUT', $path, $handler, $middleware);
    }

    public static function delete(string $path, $handler, array $middleware = []): void
    {
        self::addRoute('DELETE', $path, $handler, $middleware);
    }

    private static function addRoute(string $method, string $path, $handler, array $middleware): void
    {
        $path = self::$prefix . $path;
        $middleware = array_merge(self::$groupMiddleware, $middleware);
        self::$routes[] = compact('method', 'path', 'handler', 'middleware');
    }

    public static function group(string $prefix, array $middleware, callable $callback): void
    {
        $previousPrefix = self::$prefix;
        $previousMiddleware = self::$groupMiddleware;

        self::$prefix .= $prefix;
        self::$groupMiddleware = array_merge(self::$groupMiddleware, $middleware);

        $callback();

        self::$prefix = $previousPrefix;
        self::$groupMiddleware = $previousMiddleware;
    }

    public static function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        if ($basePath !== '/' && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
        $uri = '/' . trim($uri, '/');
        if ($uri === '/index.php') {
            $uri = '/';
        }

        $routeFound = false;

        foreach (self::$routes as $route) {
            $pattern = self::convertToRegex($route['path']);
            if ($route['method'] === $method && preg_match($pattern, $uri, $matches)) {
                $routeFound = true;
                array_shift($matches);
                $params = array_values(array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY) ? $matches : $matches);

                foreach ($route['middleware'] as $mw) {
                    $mwClass = 'App\\Middleware\\' . $mw;
                    if (class_exists($mwClass)) {
                        $instance = new $mwClass();
                        $result = $instance->handle();
                        if ($result === false) {
                            return;
                        }
                    }
                }

                if (is_array($route['handler'])) {
                    [$controller, $action] = $route['handler'];
                    $controllerClass = 'App\\Controllers\\' . $controller;
                    if (class_exists($controllerClass)) {
                        $instance = new $controllerClass();
                        call_user_func_array([$instance, $action], $params);
                    } else {
                        http_response_code(500);
                        echo "Controller {$controller} not found.";
                    }
                } elseif (is_callable($route['handler'])) {
                    call_user_func_array($route['handler'], $params);
                }
                return;
            }
        }

        if (!$routeFound) {
            http_response_code(404);
            view('errors.404');
        }
    }

    private static function convertToRegex(string $path): string
    {
        // Convert {param:pattern} syntax, default {id} to digits only
        $pattern = preg_replace_callback('/\{(\w+)(?::([^}]+))?\}/', function ($m) {
            $name = $m[1];
            $regex = $m[2] ?? ($name === 'id' ? '\d+' : '[^/]+');
            return '(?P<' . $name . '>' . $regex . ')';
        }, $path);
        return '#^' . $pattern . '$#';
    }
}
