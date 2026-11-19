<?php

namespace App\Services;

use RuntimeException;

class PostgresCopyArchive
{
    public static function read(string $dump, string $pgRestore): array
    {
        $process = proc_open([$pgRestore, '--data-only', '--schema=public', '--no-owner', '--no-privileges', '--file=-', $dump], [1 => ['pipe', 'w'], 2 => ['file', 'php://stderr', 'w']], $pipes);
        if (! is_resource($process)) {
            throw new RuntimeException('Cannot start pg_restore.');
        }
        try {
            $tables = self::parse($pipes[1]);
        } finally {
            fclose($pipes[1]);
            $exit = proc_close($process);
        }
        if ($exit !== 0) {
            throw new RuntimeException('pg_restore failed with exit code '.$exit);
        }

        return $tables;
    }

    public static function parse($stream): array
    {
        $tables = [];
        $table = null;
        while (($line = fgets($stream)) !== false) {
            $line = rtrim($line, "\r\n");
            if (preg_match('/^COPY public\.(\w+) \((.+)\) FROM stdin;$/', $line, $match)) {
                $candidate = $match[1];
                $table = self::allowed($candidate) ? $candidate : null;
                $columns = explode(', ', $match[2]);
                if ($table !== null) {
                    $tables[$table] = [];
                }
                continue;
            }
            if ($line === '\\.') {
                $table = null;
                continue;
            }
            if ($table === null) {
                continue;
            }
            $values = explode("\t", $line);
            if (count($columns) !== count($values)) {
                throw new RuntimeException('Malformed COPY row in '.$table);
            }
            $row = array_combine($columns, array_map(self::decode(...), $values));
            $key = $row['id'] ?? count($tables[$table]);
            if (isset($tables[$table][$key])) {
                $key .= '#duplicate#'.count($tables[$table]);
            }
            $tables[$table][$key] = $row;
        }

        return $tables;
    }

    public static function allowed(string $table): bool
    {
        return str_starts_with($table, 'capstone_') || in_array($table, [
            'users', 'students', 'lecturers', 'roles', 'permissions', 'model_has_roles',
            'eo_mr_ruangans', 'eo_ruangan', 'eo_mr_peminjamans',
        ], true);
    }

    private static function decode(string $value): ?string
    {
        if ($value === '\\N') {
            return null;
        }

        return preg_replace_callback('/\\\\([0-7]{1,3}|x[0-9a-fA-F]{1,2}|.)/s', function ($match) {
            $escape = $match[1];
            if (ctype_digit($escape[0])) {
                return chr(octdec($escape));
            }
            if ($escape[0] === 'x' && strlen($escape) > 1) {
                return chr(hexdec(substr($escape, 1)));
            }

            return match ($escape) {
                'b' => "\x08", 'f' => "\x0c", 'n' => "\n", 'r' => "\r", 't' => "\t", 'v' => "\x0b",
                default => $escape,
            };
        }, $value);
    }
}
