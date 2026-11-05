<?php

namespace App\Services;

use App\Helpers\Database;

class RecommendationService
{
    public function getAllRecommendations(?string $status = null): array
    {
        $sql = "SELECT * FROM recommendations";
        $params = [];
        if ($status) {
            $sql .= " WHERE status = ?";
            $params = [$status];
        }
        $sql .= " ORDER BY CASE priority WHEN 'CRITICAL' THEN 1 WHEN 'HIGH' THEN 2 WHEN 'MEDIUM' THEN 3 ELSE 4 END, id ASC";
        return Database::fetchAll($sql, $params);
    }

    public function updateStatus(int $recId, string $status): bool
    {
        return Database::execute("UPDATE recommendations SET status = ? WHERE id = ?", [$status, $recId]) > 0;
    }
}

