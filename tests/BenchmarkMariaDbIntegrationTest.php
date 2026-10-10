<?php
declare(strict_types=1);

use App\\Models\\Benchmark;
use PHPUnit\\Framework\\TestCase;

final class BenchmarkMariaDbIntegrationTest extends TestCase
{
    private static ?mysqli $db = null;
    private Benchmark $benchmarks;

    public static function setUpBeforeClass(): void
    {
        if (getenv('TINY_LLM_TEST_DB_HOST') === false) {
            self::markTestSkipped('MariaDB integration environment is not configured.');
        }

        require_once __DIR__ . '/../app/Models/Benchmark.php';
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        self::$db = new mysqli(
            (string) getenv('TINY_LLM_TEST_DB_HOST'),
            (string) (getenv('TINY_LLM_TEST_DB_USER') ?: 'root'),
            (string) (getenv('TINY_LLM_TEST_DB_PASSWORD') ?: ''),
            (string) (getenv('TINY_LLM_TEST_DB_NAME') ?: 'tiny_llm_web_test'),
            (int) (getenv('TINY_LLM_TEST_DB_PORT') ?: 3306)
        );
        self::$db->set_charset('utf8mb4');
    }

    protected function setUp(): void
    {
        $this->benchmarks = new Benchmark(self::$db);
    }

    public static function tearDownAfterClass(): void
    {
        if (self::$db instanceof mysqli) {
            self::$db->close();
        }
    }

    public function testAllJoinsMachinesModelsDatasetsAndComputesThroughput(): void
    {
        $runs = $this->benchmarks->all();

        self::assertCount(6, $runs);
        self::assertSame('test1', $runs[0]['hostname']);
        self::assertSame('TinyGPT-Test', $runs[0]['model_name']);
        self::assertSame('Test Gutenberg', $runs[0]['dataset_name']);
        self::assertEqualsWithDelta(10.0, (float) $runs[0]['steps_per_second'], 0.0001);
        self::assertSame(2, (int) $runs[0]['run_id']);
    }

    public function testCompareFiltersByDatasetModelAndTotalSteps(): void
    {
        $runs = $this->benchmarks->compare(1, 1, 10000);

        self::assertCount(3, $runs);
        self::assertSame([2, 1, 3], array_map(static fn(array $run): int => (int) $run['run_id'], $runs));
        self::assertSame(['test1', 'test1', 'test2'], array_column($runs, 'hostname'));
    }

    public function testResumedRunUsesStepsCompletedThisRunForThroughput(): void
    {
        $run = $this->benchmarks->find(2);

        self::assertNotNull($run);
        self::assertSame(1000, (int) $run['steps_this_run']);
        self::assertEqualsWithDelta(10.0, (float) $run['steps_per_second'], 0.0001);
    }

    public function testMachineAndDatasetFiltersReturnOnlyMatchingRows(): void
    {
        self::assertCount(2, $this->benchmarks->forMachine(1));
        self::assertCount(4, $this->benchmarks->forDataset(1));
        self::assertSame([], $this->benchmarks->forMachine(999));
    }

    public function testFindReturnsNullForMissingRun(): void
    {
        self::assertNull($this->benchmarks->find(999));
    }
}
