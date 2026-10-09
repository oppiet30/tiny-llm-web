<?php
declare(strict_types=1);

use Core\Router;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    public function testDispatchMatchesExactRouteAndRemovesBasePath(): void
    {
        $router = new Router();
        $router->get('/compare', static function (): void {
            echo 'comparison';
        });

        ob_start();
        $router->dispatch('GET', '/~oppie/tiny-llm-web/compare?dataset_id=1', '/~oppie/tiny-llm-web');
        $output = ob_get_clean();

        self::assertSame('comparison', $output);
    }

    public function testDispatchMatchesParameterizedRoute(): void
    {
        $router = new Router();
        $router->get('/runs/{id}', static function (string $id): void {
            echo 'run-' . $id;
        });

        ob_start();
        $router->dispatch('GET', '/runs/42');
        $output = ob_get_clean();

        self::assertSame('run-42', $output);
    }

    public function testUnknownRouteReturns404(): void
    {
        $router = new Router();
        $originalStatus = http_response_code();

        ob_start();
        $router->dispatch('GET', '/missing');
        $output = ob_get_clean();

        self::assertSame('404 - Page Not Found', $output);
        self::assertSame(404, http_response_code());

        if ($originalStatus !== false) {
            http_response_code($originalStatus);
        }
    }
}
