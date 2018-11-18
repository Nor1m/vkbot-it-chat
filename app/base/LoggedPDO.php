<?php

namespace App\base;

use App\Log;
use PDO;

/**
 * Класс PDO, который выводит запросы в лог перед выполнением
 *
 * Использует для подготовленных запросов класс {@see LoggedPdoStatement}
 *
 * @package App\base
 * @author Roman Mitasov <metas_roman@mail.ru>
 * @uses Log
 * @uses LoggedPdoStatement
 */
class LoggedPDO extends PDO
{
    public function __construct(string $dsn, string $username, string $passwd, array $options)
    {
        parent::__construct($dsn, $username, $passwd, $options);

        $this->setAttribute(static::ATTR_STATEMENT_CLASS, [LoggedPdoStatement::class]);
    }

    /**
     * @inheritdoc
     */
    public function query(
        $statement,
        $mode = PDO::ATTR_DEFAULT_FETCH_MODE,
        $arg3 = null,
        array $ctorargs = array()
    ) {
        Log::sql($statement);

        $pdo_stmt = parent::query($statement, $mode, $arg3, $ctorargs);

        if ($pdo_stmt === false) {
            Log::error('SQL error ' . $this->errorCode());
            Log::dump($this->errorInfo());
            return $pdo_stmt;
        }

        return $pdo_stmt;
    }
}
