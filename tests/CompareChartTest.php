<?php
declare(strict_types=1);

use App\Helpers\BenchmarkStatistics;
use App\Helpers\CompareChartMode;
use PHPUnit\Framework\TestCase;

final class CompareChartTest extends TestCase
{
    public function testModeDefaultsToAverageForMissingOrInvalidInput(): void
    {
        foreach ([null, false, '', 'invalid', 'RUNS', ['runs'], 1] as $value) {
            self::assertSame('average', CompareChartMode::normalize($value));
        }
        self::assertSame('average', CompareChartMode::normalize('average'));
        self::assertSame('runs', CompareChartMode::normalize('runs'));
    }

    public function testAverageChartGroupsRepeatedRunsAndScalesByFastestMean(): void
    {
        $html = $this->render('average', [$this->run(1, 1, 10), $this->run(2, 1, 20), $this->run(3, 2, 5)]);
        self::assertStringContainsString('Machine average throughput', $html);
        self::assertStringContainsString('value="average" selected', $html);
        self::assertSame(2, substr_count($html, 'class="throughput-row"'));
        self::assertStringContainsString('15.000 steps/sec', $html);
        self::assertStringContainsString('width: 100.0000%', $html);
        self::assertStringContainsString('width: 33.3333%', $html);
        self::assertStringContainsString('Matching runs (3)', $html);
        self::assertStringContainsString('Repeat-run statistics', $html);
        self::assertSame(substr_count($html, '<section '), substr_count($html, '</section>'));
    }

    public function testRunChartKeepsRepeatedRunsAndScalesByFastestRun(): void
    {
        $html = $this->render('runs', [$this->run(1, 1, 10), $this->run(2, 1, 20), $this->run(3, 2, 5)]);
        self::assertStringContainsString('Individual run throughput', $html);
        self::assertStringContainsString('value="runs" selected', $html);
        self::assertSame(3, substr_count($html, 'class="throughput-row"'));
        self::assertStringContainsString('href="/runs/2">Run #2</a>', $html);
        foreach (['100.0000', '50.0000', '25.0000'] as $width) {
            self::assertStringContainsString('width: ' . $width . '%', $html);
        }
        self::assertSame(substr_count($html, '<section '), substr_count($html, '</section>'));
    }

    public function testInvalidMeasurementsAreExcludedFromRunBars(): void
    {
        $runs = [$this->run(1, 1, 10)];
        foreach ([null, 0, -1, INF, NAN, 'invalid'] as $index => $value) {
            $runs[] = $this->run($index + 2, 1, $value);
        }
        $html = $this->render('runs', $runs);
        self::assertSame(1, substr_count($html, 'class="throughput-row"'));
        self::assertStringContainsString('Matching runs (7)', $html);
    }

    public function testMissingMeasurementsShowChartEmptyStates(): void
    {
        foreach (['average' => 'No valid machine-average measurements', 'runs' => 'No valid per-run throughput measurements'] as $mode => $message) {
            $html = $this->render($mode, [$this->run(1, 1, null)]);
            self::assertStringContainsString($message, $html);
            self::assertStringNotContainsString('class="throughput-row"', $html);
            self::assertStringContainsString('Matching runs (1)', $html);
        }
    }

    public function testEmptyComparisonShowsNoMatchingRuns(): void
    {
        foreach (['average', 'runs'] as $mode) {
            $html = $this->render($mode, []);
            self::assertStringContainsString('No benchmark runs match these filters.', $html);
            self::assertStringNotContainsString('class="throughput-chart"', $html);
        }
    }

    public function testChartEscapesHostnamesInBothModes(): void
    {
        $run = $this->run(1, 1, 10);
        $run['hostname'] = '<script>alert(1)</script>';
        foreach (['average', 'runs'] as $mode) {
            $html = $this->render($mode, [$run]);
            self::assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $html);
            self::assertStringNotContainsString('<script>alert(1)</script>', $html);
        }
    }

    private function run(int $id, int $machine, mixed $throughput): array
    {
        return [
            'run_id' => $id, 'machine_id' => $machine, 'hostname' => 'machine-' . $machine,
            'steps_per_second' => $throughput, 'cpu_model' => 'Test CPU',
            'real_seconds' => null, 'train_loss' => null, 'validation_loss' => null,
            'run_date' => '2026-10-01 12:00:00',
        ];
    }

    private function render(string $chartMode, array $benchmarks): string
    {
        $datasets = [['dataset_id' => 1, 'name' => 'Test dataset']];
        $models = [['model_id' => 1, 'name' => 'Test model']];
        $datasetId = 1;
        $modelId = 1;
        $trainingSteps = 10000;
        $statistics = BenchmarkStatistics::byMachine($benchmarks);
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', '');
        }
        ob_start();
        try {
            require __DIR__ . '/../app/Views/compare.php';
            return (string) ob_get_contents();
        } finally {
            ob_end_clean();
        }
    }
}
