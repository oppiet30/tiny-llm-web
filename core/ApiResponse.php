<?php
declare(strict_types=1);

namespace Core;

final class ApiResponse
{
    public static function isApiPath(string $path): bool
    {
        return $path === '/api' || str_starts_with($path, '/api/');
    }

    public static function validId(string $id): bool
    {
        if (!ctype_digit($id)) {
            return false;
        }
        $digits = ltrim($id, '0');
        $maximum = (string) PHP_INT_MAX;
        return $digits !== '' && (strlen($digits) < strlen($maximum)
            || (strlen($digits) === strlen($maximum) && strcmp($digits, $maximum) <= 0));
    }

    public static function error(int $status, string $message): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => $message], JSON_THROW_ON_ERROR);
    }
}
