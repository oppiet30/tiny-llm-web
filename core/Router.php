<?php
declare(strict_types=1);

namespace Core;

class Router
{
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, callable $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    private function add(
        string $method,
        string $path,
        callable $handler
    ): void {
        $this->routes[$method][$path] = $handler;
    }

    public function dispatch(
        string $method,
        string $uri,
        string $basePath = ''
    ): void {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';

        if (
            $basePath !== ''
            && (
                $path === $basePath
                || str_starts_with($path, $basePath . '/')
            )
        ) {
            $path = substr($path, strlen($basePath));
        }

        $path = '/' . trim($path, '/');

        // Match exact routes first.
        if (isset($this->routes[$method][$path])) {
            call_user_func($this->routes[$method][$path]);
            return;
        }

        // Match routes containing parameters.
        foreach ($this->routes[$method] ?? [] as $route => $handler) {
            if (!str_contains($route, '{')) {
                continue;
            }

            $pattern = preg_quote($route, '#');

            $pattern = preg_replace(
                '/\\\\\{[a-zA-Z_][a-zA-Z0-9_]*\\\\\}/',
                '([^/]+)',
                $pattern
            );

            if ($pattern === null) {
                continue;
            }

            if (preg_match('#^' . $pattern . '$#D', $path, $matches)) {
                array_shift($matches);
                call_user_func_array($handler, $matches);
                return;
            }
        }

        http_response_code(404);
        echo '404 - Page Not Found';
    }
}
