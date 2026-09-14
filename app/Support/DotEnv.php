<?php

namespace App\Support;

// Minimal .env reader/writer used by the web installer, so a non-technical
// admin never has to hand-edit the file over cPanel File Manager.
class DotEnv
{
    public static function path(): string
    {
        return base_path('.env');
    }

    public static function ensureExists(): void
    {
        if (! file_exists(self::path()) && file_exists(base_path('.env.example'))) {
            copy(base_path('.env.example'), self::path());
        }
    }

    /** Read all key => value pairs from .env (raw, unquoted). */
    public static function read(): array
    {
        self::ensureExists();
        $out = [];
        foreach (file(self::path(), FILE_IGNORE_NEW_LINES) ?: [] as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#') || ! str_contains($line, '=')) {
                continue;
            }
            [$k, $v] = explode('=', $line, 2);
            $v = trim($v);
            if (strlen($v) >= 2 && ($v[0] === '"' || $v[0] === "'") && $v[-1] === $v[0]) {
                $v = substr($v, 1, -1);
            }
            $out[trim($k)] = $v;
        }
        return $out;
    }

    /** Set (or add) one or more keys, preserving the rest of the file. */
    public static function set(array $values): void
    {
        self::ensureExists();
        $contents = file_get_contents(self::path());

        foreach ($values as $key => $value) {
            $line = $key . '=' . self::quote($value);
            $pattern = '/^' . preg_quote($key, '/') . '=.*$/m';
            if (preg_match($pattern, $contents)) {
                $contents = preg_replace($pattern, $line, $contents);
            } else {
                $contents = rtrim($contents, "\n") . "\n" . $line . "\n";
            }
        }

        file_put_contents(self::path(), $contents);
    }

    private static function quote(?string $value): string
    {
        $value = (string) $value;
        // Quote when the value has spaces or characters the parser dislikes.
        if ($value === '' || preg_match('/\s|#|"|\'|=/', $value)) {
            return '"' . str_replace('"', '\"', $value) . '"';
        }
        return $value;
    }
}
