<?php
declare(strict_types=1);

namespace App\Models;

use mysqli;

class Machine
{
    public function __construct(
        private mysqli $db
    ) {}

    public function all(): array
    {
        $sql = "
            SELECT
                machine_id,
                hostname,
                cpu_model,
                cpu_cores,
                cpu_threads,
                ram_mb,
                operating_system,
                notes
            FROM machines
            ORDER BY hostname ASC
        ";

        return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }
}
