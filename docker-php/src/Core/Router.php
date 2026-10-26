<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, callable $callback): void
    {
        $this->addRoute('GET', $path, $callback);
    }

    public function post(string $path, callable $callback): void
    {
        $this->addRoute('POST', $path, $callback);
    }

    private function addRoute(
        string $method,
        string $path,
        callable $callback
    ): void {
        $this->routes[$method][$path] = $callback;
    }


    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $method = strtoupper($method);

        foreach ($this->routes[$method] ?? [] as $route => $callback) {
            $pattern = preg_replace(
                '/\{[a-zA-Z_][a-zA-Z0-9_]*\}/',
                '([0-9]+)',
                $route
            );

            if (preg_match('#^' . $pattern . '$#', $path, $matches)) {
                array_shift($matches);

                $callback(...array_map('intval', $matches));
                return;
            }
        }

        http_response_code(404);

        echo '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>404 - Página no encontrada</title>
        <link rel="stylesheet" href="/assets/style.css">
    </head>
    <body>
        <main class="container">
            <section class="card">
                <h1>404</h1>
                <h2>Página no encontrada</h2>
                <p class="muted">
                    La página que buscas no existe o ha sido movida.
                </p>
                <a class="button" href="/">Volver al inicio</a>
            </section>
        </main>
    </body>
    </html>';
    }

}
