<?php

namespace App\Helpers;

class Env
{
    protected static array $variables = [];

    public static function load(string $path): void
    {
        if (!file_exists($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (str_contains($line, '=')) {
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Strip quotes if present
                if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                    (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                    $value = substr($value, 1, -1);
                }

                self::$variables[$key] = $value;
                if (!isset($_ENV[$key])) {
                    $_ENV[$key] = $value;
                }
                putenv("$key=$value");
            }
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        if (isset(self::$variables[$key])) {
            return self::castValue(self::$variables[$key]);
        }
        $val = getenv($key);
        if ($val !== false) {
            return self::castValue($val);
        }
        return $default;
    }

    private static function castValue(string $val): mixed
    {
        return match (strtolower($val)) {
            'true', '(true)' => true,
            'false', '(false)' => false,
            'null', '(null)' => null,
            'empty', '(empty)' => '',
            default => $val,
        };
    }
}

