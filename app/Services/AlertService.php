<?php

namespace App\Services;

use App\Helpers\Database;

class AlertService
{
    public function getAllAlerts(?string $status = null): array
    {
        $sql = "SELECT * FROM alerts";
        $params = [];
        if ($status) {
            $sql .= " WHERE status = ?";
            $params = [$status];
        }
        $sql .= " ORDER BY CASE severity WHEN 'CRITICAL' THEN 1 WHEN 'HIGH' THEN 2 WHEN 'MEDIUM' THEN 3 ELSE 4 END, id DESC";
        return Database::fetchAll($sql, $params);
    }

    public function updateStatus(int $alertId, string $status): bool
    {
        return Database::execute("UPDATE alerts SET status = ? WHERE id = ?", [$status, $alertId]) > 0;
    }

    public function getActiveCounts(): array
    {
        $rows = Database::fetchAll("SELECT severity, COUNT(*) as count FROM alerts WHERE status = 'ACTIVE' GROUP BY severity");
        $counts = ['CRITICAL' => 0, 'HIGH' => 0, 'MEDIUM' => 0, 'LOW' => 0, 'TOTAL' => 0];
        foreach ($rows as $r) {
            $counts[$r['severity']] = (int) $r['count'];
            $counts['TOTAL'] += (int) $r['count'];
        }
        return $counts;
    }
}

