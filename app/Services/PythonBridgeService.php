<?php

namespace App\Services;

use App\Helpers\Env;

class PythonBridgeService
{
    protected string $pythonPath;
    protected string $pythonScript;

    public function __construct()
    {
        $this->pythonPath = Env::get('PYTHON_PATH', 'python');
        $this->pythonScript = dirname(__DIR__, 2) . '/python/main.py';
    }

    public function runCommand(string $command, array $args = []): array
    {
        $argString = '';
        foreach ($args as $key => $val) {
            $argString .= ' --' . escapeshellarg($key) . ' ' . escapeshellarg((string) $val);
        }

        $cmd = sprintf(
            '"%s" "%s" %s%s 2>&1',
            $this->pythonPath,
            $this->pythonScript,
            escapeshellcmd($command),
            $argString
        );

        $output = shell_exec($cmd);

        if (!$output) {
            return ['success' => false, 'message' => 'No output received from Python analytics engine.'];
        }

        // Try extracting JSON from output
        $startPos = strpos($output, '{');
        if ($startPos !== false) {
            $jsonStr = substr($output, $startPos);
            $decoded = json_decode($jsonStr, true);
            if ($decoded !== null) {
                return ['success' => true, 'data' => $decoded, 'raw' => $output];
            }
        }

        return ['success' => true, 'raw' => $output];
    }

    public function runAllAnalytics(): array
    {
        return $this->runCommand('run-all');
    }

    public function runImport(int $importId, string $filePath): array
    {
        return $this->runCommand('import', [
            'import-id' => $importId,
            'file' => $filePath
        ]);
    }

    public function whyChange(?string $p1 = null, ?string $p2 = null): array
    {
        $params = [];
        if ($p1) $params['period1'] = $p1;
        if ($p2) $params['period2'] = $p2;
        return $this->runCommand('why-change', $params);
    }
}

