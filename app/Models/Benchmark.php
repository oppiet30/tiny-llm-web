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
    public function forModel(int $modelId): array
    {
        $sql = "
            SELECT
                b.run_id,
                b.machine_id,
                m.hostname,
                d.name AS dataset_name,
                b.training_steps,
                b.steps_this_run,
                b.real_seconds,
                b.train_loss,
                b.validation_loss,
                b.run_date,
                COALESCE(b.steps_this_run, b.training_steps)
                    / NULLIF(b.real_seconds, 0) AS steps_per_second
            FROM benchmark_runs b
            JOIN machines m ON m.machine_id = b.machine_id
            JOIN datasets d ON d.dataset_id = b.dataset_id
            WHERE b.model_id = ?
            ORDER BY b.run_date DESC, b.run_id DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $modelId);
        $stmt->execute();

        $benchmarks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $benchmarks;
    }
    public function forDataset(int $datasetId): array
    {
        $sql = "
            SELECT
                b.run_id,
                b.machine_id,
                b.model_id,
                m.hostname,
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
            JOIN machines m ON m.machine_id = b.machine_id
            JOIN models mo ON mo.model_id = b.model_id
            WHERE b.dataset_id = ?
            ORDER BY b.run_date DESC, b.run_id DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $datasetId);
        $stmt->execute();

        $benchmarks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $benchmarks;
    }
    /**
     * Return individual runs for one comparable dataset/model/step-count group.
     *
     * Repeated measurements are kept as separate rows.
     */
    public function compare(int $datasetId, int $modelId, int $trainingSteps): array
    {
        $sql = "
            SELECT
                b.run_id,
                b.machine_id,
                m.hostname,
                m.cpu_model,
                b.dataset_id,
                d.name AS dataset_name,
                b.model_id,
                mo.name AS model_name,
                b.start_step,
                b.training_steps,
                b.steps_this_run,
                b.real_seconds,
                b.train_loss,
                b.validation_loss,
                b.run_date,
                COALESCE(b.steps_this_run, b.training_steps)
                    / NULLIF(b.real_seconds, 0) AS steps_per_second
            FROM benchmark_runs b
            JOIN machines m ON m.machine_id = b.machine_id
            JOIN datasets d ON d.dataset_id = b.dataset_id
            JOIN models mo ON mo.model_id = b.model_id
            WHERE b.dataset_id = ?
              AND b.model_id = ?
              AND b.training_steps = ?
            ORDER BY steps_per_second DESC, b.run_id DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('iii', $datasetId, $modelId, $trainingSteps);
        $stmt->execute();
        $runs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $runs;
    }


    /**
     * Return benchmark runs ordered by date, with optional dataset/model/machine filters.
     */
    public function history(?int $datasetId = null, ?int $modelId = null, ?int $machineId = null): array
    {
        $sql = "
            SELECT
                b.run_id, b.machine_id, m.hostname, b.dataset_id,
                d.name AS dataset_name, b.model_id, mo.name AS model_name,
                b.training_steps, b.steps_this_run, b.real_seconds, b.run_date,
                COALESCE(b.steps_this_run, b.training_steps)
                    / NULLIF(b.real_seconds, 0) AS steps_per_second
            FROM benchmark_runs b
            JOIN machines m ON m.machine_id = b.machine_id
            JOIN datasets d ON d.dataset_id = b.dataset_id
            JOIN models mo ON mo.model_id = b.model_id
        ";
        $conditions = [];
        $types = '';
        $values = [];
        foreach ([
            ['b.dataset_id', $datasetId],
            ['b.model_id', $modelId],
            ['b.machine_id', $machineId],
        ] as [$column, $value]) {
            if ($value !== null) {
                $conditions[] = $column . ' = ?';
                $types .= 'i';
                $values[] = $value;
            }
        }
        if ($conditions !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }
        $sql .= ' ORDER BY b.run_date ASC, b.run_id ASC';

        $stmt = $this->db->prepare($sql);
        if ($values !== []) {
            $stmt->bind_param($types, ...$values);
        }
        $stmt->execute();
        $runs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $runs;
    }


    public function find(int $id): ?array
    {
        $sql = "
            SELECT
                b.*,
                m.hostname,
                m.cpu_model,
                mo.name AS model_name,
                d.name AS dataset_name,
                COALESCE(b.steps_this_run, b.training_steps)
                    / NULLIF(b.real_seconds, 0) AS steps_per_second
            FROM benchmark_runs b
            JOIN machines m ON m.machine_id = b.machine_id
            JOIN models mo ON mo.model_id = b.model_id
            JOIN datasets d ON d.dataset_id = b.dataset_id
            WHERE b.run_id = ?
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();

        $run = $stmt->get_result()->fetch_assoc();

        $stmt->close();

        return $run ?: null;
    }
}
