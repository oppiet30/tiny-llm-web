<?php
declare(strict_types=1);

namespace App\Models;

use mysqli;

class Benchmark
{
    public function __construct(
        private mysqli $db
    ) {}

    public function all(): array
    {
        $sql = "
            SELECT
                b.run_id,
                m.hostname,
                m.cpu_model,
                mo.name AS model_name,
                d.name AS dataset_name,
                b.training_steps,
                b.real_seconds,
                b.train_loss,
                b.validation_loss,
                b.run_date,
                COALESCE(b.steps_this_run, b.training_steps)
                    / NULLIF(b.real_seconds, 0)
                    AS steps_per_second
            FROM benchmark_runs b
            JOIN machines m
                ON m.machine_id = b.machine_id
            JOIN models mo
                ON mo.model_id = b.model_id
            JOIN datasets d
                ON d.dataset_id = b.dataset_id
            ORDER BY steps_per_second DESC
        ";

        return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function forMachine(int $machineId): array
    {
        $sql = "
            SELECT
                b.run_id,
                d.name AS dataset_name,
                mo.name AS model_name,
                b.training_steps,
                b.steps_this_run,
                b.real_seconds,
                b.train_loss,
                b.validation_loss,
                b.run_date,
                COALESCE(b.steps_this_run, b.training_steps)
                    / NULLIF(b.real_seconds, 0) AS steps_per_second
            FROM benchmark_runs b
            JOIN datasets d ON d.dataset_id = b.dataset_id
            JOIN models mo ON mo.model_id = b.model_id
            WHERE b.machine_id = ?
            ORDER BY b.run_date DESC, b.run_id DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $machineId);
        $stmt->execute();

        $benchmarks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $benchmarks;
    }
}
