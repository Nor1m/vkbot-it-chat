<?php

namespace App\base;

use PDO;

class Db
{
    private static $_PDO;

    public static function init($db, $host, $dbname, $user, $pass, $pdoOptions = []): void
    {
        self::$_PDO = new PDO("$db:host=$host;dbname=$dbname", $user, $pass, $pdoOptions);
    }

    /**
     * @return PDO
     */
    public static function pdo(): PDO
    {
        return self::$_PDO;
    }
}