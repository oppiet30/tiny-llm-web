<?php
declare(strict_types=1);

namespace App\Models;

use mysqli;

class Model
{
    public function __construct(
        private mysqli $db
    ) {}

    public function all(): array
    {
        $sql = "
            SELECT *
            FROM models
            ORDER BY model_id ASC
        ";

        return $this->db
            ->query($sql)
            ->fetch_all(MYSQLI_ASSOC);
    }
    public function find(int $id): ?array
    {
        $sql = "
            SELECT *
            FROM models
            WHERE model_id = ?
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();

        $model = $stmt->get_result()->fetch_assoc();

        $stmt->close();

        return $model ?: null;
    }
}
