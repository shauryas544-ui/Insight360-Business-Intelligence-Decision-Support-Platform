<?php

namespace App\Services;

use App\Helpers\Database;

class ImportService
{
    public function getImportHistory(): array
    {
        $query = "
            SELECT
                di.*,
                u.name as uploader_name
            FROM data_imports di
            LEFT JOIN users u ON di.imported_by = u.id
            ORDER BY di.id DESC
        ";
        return Database::fetchAll($query);
    }

    public function getImportDetails(int $importId): ?array
    {
        $import = Database::fetchOne("SELECT * FROM data_imports WHERE id = ?", [$importId]);
        if (!$import) {
            return null;
        }

        $qualityLogs = Database::fetchAll("SELECT * FROM data_quality_logs WHERE import_id = ?", [$importId]);
        $import['quality_logs'] = $qualityLogs;
        $import['error_log_parsed'] = $import['error_log'] ? json_decode($import['error_log'], true) : [];

        return $import;
    }

    public function handleUpload(array $file, ?int $userId = null): array
    {
        // 1. Validation of upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Upload failed with error code ' . $file['error']];
        }

        $fileName = basename($file['name']);
        $fileSize = $file['size'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($ext, ['csv', 'txt'])) {
            return ['success' => false, 'message' => 'Invalid file format. Only standard CSV files are accepted.'];
        }

        $targetDir = dirname(__DIR__, 2) . '/storage/temporary';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $safeName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '', $fileName);
        $targetPath = $targetDir . '/' . $safeName;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            // fallback if copy needed
            copy($file['tmp_name'], $targetPath);
        }

        // 2. Insert into data_imports
        Database::execute(
            "INSERT INTO data_imports (filename, file_type, file_size, status, imported_by)
             VALUES (?, ?, ?, 'VALIDATING', ?)",
            [$fileName, $ext, $fileSize, $userId]
        );
        $importId = (int) Database::lastInsertId();

        // 3. Trigger Python ETL Transformer
        $bridge = new PythonBridgeService();
        $res = $bridge->runImport($importId, $targetPath);

        return [
            'success' => true,
            'import_id' => $importId,
            'result' => $res
        ];
    }
}

