<?php

namespace App\Helpers;

class Router
{
    protected static array $routes = [];
    protected static array $groupMiddleware = [];
    protected static string $groupPrefix = '';

    public static function group(array $attributes, callable $callback): void
    {
        $previousPrefix = self::$groupPrefix;
        $previousMiddleware = self::$groupMiddleware;

        if (isset($attributes['prefix'])) {
            self::$groupPrefix = $previousPrefix . '/' . trim($attributes['prefix'], '/');
        }

        if (isset($attributes['middleware'])) {
            $middleware = is_array($attributes['middleware']) ? $attributes['middleware'] : [$attributes['middleware']];
            self::$groupMiddleware = array_merge(self::$groupMiddleware, $middleware);
        }

        call_user_func($callback);

        self::$groupPrefix = $previousPrefix;
        self::$groupMiddleware = $previousMiddleware;
    }

    public static function get(string $path, mixed $action, array $middleware = []): void
    {
        self::addRoute('GET', $path, $action, $middleware);
    }

    public static function post(string $path, mixed $action, array $middleware = []): void
    {
        self::addRoute('POST', $path, $action, $middleware);
    }

    public static function put(string $path, mixed $action, array $middleware = []): void
    {
        self::addRoute('PUT', $path, $action, $middleware);
    }

    public static function delete(string $path, mixed $action, array $middleware = []): void
    {
        self::addRoute('DELETE', $path, $action, $middleware);
    }

    protected static function addRoute(string $method, string $path, mixed $action, array $middleware = []): void
    {
        $fullPath = self::$groupPrefix . '/' . trim($path, '/');
        $fullPath = '/' . trim($fullPath, '/');
        if ($fullPath === '') {
            $fullPath = '/';
        }

        $allMiddleware = array_merge(self::$groupMiddleware, $middleware);

        self::$routes[] = [
            'method' => $method,
            'path' => $fullPath,
            'action' => $action,
            'middleware' => $allMiddleware,
        ];
    }

    public static function dispatch(Request $request): void
    {
        $method = $request->method();
        // Method spoofing for PUT/DELETE via POST _method field
        if ($method === 'POST' && $request->input('_method')) {
            $method = strtoupper((string) $request->input('_method'));
        }

        $uri = $request->uri();

        foreach (self::$routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Run Middlewares
                foreach ($route['middleware'] as $mw) {
                    $mwInstance = self::resolveMiddleware($mw);
                    if ($mwInstance && !$mwInstance->handle($request)) {
                        return; // Middleware handled the response/redirect
                    }
                }

                // Execute Action
                self::executeAction($route['action'], $request, $params);
                return;
            }
        }

        // No route matched
        if ($request->isJson() || str_starts_with($uri, '/api/')) {
            Response::error('Endpoint not found', 404);
        } else {
            http_response_code(404);
            $notFoundView = dirname(__DIR__, 2) . '/app/Views/errors/404.php';
            if (file_exists($notFoundView)) {
                require $notFoundView;
            } else {
                echo '<div style="font-family:sans-serif; text-align:center; padding:50px;"><h2>404 - Page Not Found</h2><p><a href="/">Return to Dashboard</a></p></div>';
            }
        }
    }

    protected static function resolveMiddleware(string $middleware): ?object
    {
        $args = [];
        if (str_contains($middleware, ':')) {
            [$name, $argString] = explode(':', $middleware, 2);
            $args = explode(',', $argString);
        } else {
            $name = $middleware;
        }

        return match ($name) {
            'auth' => new \App\Middleware\AuthMiddleware(),
            'role' => new \App\Middleware\RoleMiddleware($args),
            default => null,
        };
    }

    protected static function executeAction(mixed $action, Request $request, array $params): void
    {
        if (is_callable($action)) {
            call_user_func_array($action, array_merge([$request], $params));
            return;
        }

        if (is_array($action)) {
            [$controllerClass, $method] = $action;
            $controller = new $controllerClass();
            call_user_func_array([$controller, $method], array_merge([$request], $params));
            return;
        }

        throw new \Exception("Invalid route action specified.");
    }
}

