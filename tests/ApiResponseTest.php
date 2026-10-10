<?php
declare(strict_types=1);

use Core\ApiResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ApiResponseTest extends TestCase
{
    public static function identifiers(): array
    {
        return [
            ['1', true], ['0001', true], [(string) PHP_INT_MAX, true],
            ['0', false], ['000', false], ['-1', false], ['1.0', false],
            ['1e3', false], ['abc', false], ['', false],
            [(string) PHP_INT_MAX . '0', false], ['18446744073709551615', false],
        ];
    }

    #[DataProvider('identifiers')]
    public function testIdsCannotBeTruncatedWhenConvertedToIntegers(string $id, bool $valid): void
    {
        self::assertSame($valid, ApiResponse::validId($id));
    }
}
