<?php

namespace App\base;

/**
 * Класс обёртка для PDO
 * @package App\base
 * @author Roman Mitasov <metas_roman@mail.ru>
 * @uses LoggedPDO
 */
class Db
{
    private static $_PDO;

    /**
     * Инициализация подключения к БД
     *
     * Создаёт экземпляр PDO и сохраняет его для последующих вызовов
     *
     * @param string $db
     * @param string $host
     * @param string $dbname
     * @param string $user
     * @param string $pass
     * @param array $pdoOptions
     */
    public static function init(
        $db,
        $host,
        $dbname,
        $user,
        $pass,
        $pdoOptions = []
    ): void {
        self::$_PDO = new LoggedPDO("$db:host=$host;dbname=$dbname", $user, $pass, $pdoOptions);
    }

    /**
     * Получить сущность PDO
     * @return LoggedPDO
     */
    public static function pdo(): LoggedPDO
    {
        return self::$_PDO;
    }
}
