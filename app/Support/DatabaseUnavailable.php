<?php

namespace App\Support;

use Illuminate\Database\DetectsLostConnections;
use Illuminate\Database\QueryException;

/**
 * Is this exception a failure to reach the database (rather than a bad query)?
 * Covers refused / dropped / timed-out connections, too many connections and
 * wrong credentials - every case where no page that needs the database can work.
 */
final class DatabaseUnavailable
{
    use DetectsLostConnections;

    /** MySQL client/server error codes that mean "can't talk to the database". */
    private const CONNECTION_ERROR_CODES = [
        1040, // too many connections
        1044, // access denied to database
        1045, // access denied for user (wrong credentials)
        1049, // unknown database
        2002, // can't connect (refused / socket missing / timeout)
        2003, // can't connect to host
        2005, // unknown host
        2006, // server has gone away
        2013, // lost connection during query
    ];

    public static function matches(\Throwable $e): bool
    {
        for ($current = $e; $current; $current = $current->getPrevious()) {
            if (! $current instanceof \PDOException && ! $current instanceof QueryException) {
                continue;
            }

            $driverCode = (int) ($current->errorInfo[1] ?? 0);
            if (in_array($driverCode, self::CONNECTION_ERROR_CODES, true)
                || preg_match('/SQLSTATE\[(HY000|08\d{3})\] \[(1040|1044|1045|1049|2002|2003|2005|2006|2013)\]/', $current->getMessage())
                || (new self)->causedByLostConnection($current)) {
                return true;
            }
        }

        return false;
    }
}
