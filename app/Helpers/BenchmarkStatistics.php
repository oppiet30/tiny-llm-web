<?php
declare(strict_types=1);

namespace App\Helpers;

/**
 * Summarize valid throughput measurements for each machine.
 * Sample standard deviation uses n-1; it is undefined for a single run.
 */
final class BenchmarkStatistics
{
    public static function byMachine(array $runs): array
    {
        $groups = [];
        foreach ($runs as $run) {
            $value = $run['steps_per_second'] ?? null;
            if ($value === null || !is_numeric($value) || !is_finite((float) $value) || (float) $value <= 0) {
                continue;
            }

            $id = (int) $run['machine_id'];
            if (!isset($groups[$id])) {
                $groups[$id] = [
                    'machine_id' => $id,
                    'hostname' => (string) $run['hostname'],
                    'values' => [],
                ];
            }
            $groups[$id]['values'][] = (float) $value;
        }

        $result = [];
        foreach ($groups as $group) {
            $values = $group['values'];
            $count = count($values);
            $mean = array_sum($values) / $count;
            $sumSquared = 0.0;
            foreach ($values as $value) {
                $sumSquared += ($value - $mean) ** 2;
            }

            $result[] = [
                'machine_id' => $group['machine_id'],
                'hostname' => $group['hostname'],
                'count' => $count,
                'mean' => $mean,
                'min' => min($values),
                'max' => max($values),
                'stddev' => $count > 1 ? sqrt($sumSquared / ($count - 1)) : null,
            ];
        }

        usort($result, static fn (array $a, array $b): int =>
            ($b['mean'] <=> $a['mean']) ?: ($a['machine_id'] <=> $b['machine_id'])
        );

        return $result;
    }
}
