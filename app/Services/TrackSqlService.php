<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class TrackSqlService
{
    private static bool $isLogging = false;

    /**
     * Record an executed modifying SQL query into trackersql
     */
    public static function record(string $sql, ?string $user = null): void
    {
        if (self::$isLogging) {
            return;
        }

        self::$isLogging = true;
        try {
            $ip = request()?->ip() ?? '127.0.0.1';
            $currentUser = $user ?: (session('auth_user.nama') ?: (session('auth_user.kode') ?: 'Admin Utama'));
            
            DB::table('trackersql')->insert([
                'tanggal' => now(),
                'sqle'    => $ip . ' ' . trim($sql),
                'usere'   => mb_substr((string)$currentUser, 0, 20),
            ]);
        } catch (\Throwable $e) {
            // Silently ignore logging errors to prevent breaking main transaction
        } finally {
            self::$isLogging = false;
        }
    }

    /**
     * Format SQL query with bindings substituted safely
     */
    public static function formatSqlWithBindings(string $sql, array $bindings = []): string
    {
        if (empty($bindings)) {
            return $sql;
        }

        foreach ($bindings as $binding) {
            if (is_null($binding)) {
                $val = 'NULL';
            } elseif (is_numeric($binding)) {
                $val = (string)$binding;
            } elseif (is_bool($binding)) {
                $val = $binding ? '1' : '0';
            } else {
                $val = "'" . addslashes((string)$binding) . "'";
            }
            $sql = preg_replace('/\\?/', $val, $sql, 1);
        }

        return $sql;
    }

    /**
     * Handle DB::listen query event automatically for all INSERT, UPDATE, DELETE, and REPLACE
     */
    public static function handleQueryEvent($query): void
    {
        if (self::$isLogging) {
            return;
        }

        $sqlTrim = ltrim($query->sql);
        $firstSpace = strpos($sqlTrim, ' ');
        $verb = strtoupper($firstSpace !== false ? substr($sqlTrim, 0, $firstSpace) : substr($sqlTrim, 0, 6));

        if (!in_array($verb, ['INSERT', 'UPDATE', 'DELETE', 'REPLACE'])) {
            return;
        }

        // Exclude internal/tracking tables
        $ignoredPatterns = ['trackersql', 'tracker', 'sessions', 'migrations', 'failed_jobs', 'telescope_'];
        foreach ($ignoredPatterns as $pattern) {
            if (stripos($sqlTrim, $pattern) !== false) {
                return;
            }
        }

        $formattedSql = self::formatSqlWithBindings($query->sql, $query->bindings ?? []);
        self::record($formattedSql);
    }
}
