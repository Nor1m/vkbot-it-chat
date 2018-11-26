<?php

namespace App\base;

use InvalidArgumentException;
use PDO;
use PDOStatement;

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

    /**
     * Получить запрос
     *
     * Возвращает подготовленный PDOStatement со
     * вставленными параметрами
     *
     * @param string $sql Текст запроса
     * @param array|null $params Параметры запроса
     * @return PDOStatement
     */
    public static function query(string $sql, ?array $params = null): PDOStatement
    {
        if (!$params) {
            return self::pdo()->query($sql);
        }

        $stmt = self::pdo()->prepare($sql);
        self::prepareParams($stmt, $params);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Выбрать всё
     *
     * @param string $sql Текст запроса
     * @param array|null $params Параметры запроса
     * @param int|null $fetchStyle
     * @param null $arg1
     * @param array $ctorArgs
     * @return array
     */
    public static function queryAll(
        string $sql,
        ?array $params = null,
        int $fetchStyle = null,
        $arg1 = null,
        $ctorArgs = []
    ): array {
        $stmt = self::query($sql, $params);
        $res = $stmt->fetchAll($fetchStyle, $arg1, $ctorArgs);
        $stmt->closeCursor();
        return $res;
    }

    /**
     * Выбрать строку
     *
     * @param string $sql Текст запроса
     * @param array $params Параметры запроса
     * @param $fetchStyle
     * @return null
     */
    public static function queryRow(
        string $sql,
        array $params = null,
        $fetchStyle = null
    ) {
        $stmt = self::query($sql, $params);
        if ($stmt->rowCount() < 1) {
            $stmt->closeCursor();
            return null;
        }

        $res = $stmt->fetch($fetchStyle);
        $stmt->closeCursor();
        return $res;
    }

    /**
     * Выборка объекта
     *
     * @param string $sql Текст запроса
     * @param array|null $params Параметры запроса
     * @param string $className Имя класса, объект которого создастся
     * @param array $ctorArgs Аргументы конструктора класса
     * @return mixed Объект
     */
    public static function queryObject(
        string $sql,
        array $params = null,
        $className = \stdClass::class,
        $ctorArgs = []
    ) {
        $stmt = self::query($sql, $params);
        if ($stmt->rowCount() < 1) {
            $stmt->closeCursor();
            return null;
        }
        $res = $stmt->fetchObject($className, $ctorArgs);
        $stmt->closeCursor();
        return $res;
    }

    /**
     * Выбирает 1 значение из первой строки
     *
     * По умолчанию - значение из 1-й ячейки
     *
     * @param string $sql Текст запроса
     * @param array|null $params Параметры запроса
     * @param int $columnNumber Номер ячейки, из которой выбрать значение, начинается с 0
     * @return mixed
     */
    public static function queryScalar(string $sql, ?array $params = null, int $columnNumber = 0)
    {
        $st = self::query($sql, $params);
        $res = $st->fetchColumn($columnNumber);
        $st->closeCursor();
        return $res;
    }

    /**
     * Выполнить запрос, не возвращающий данные
     *
     * Таким запросом могут быть INSERT, UPDATE, DELETE
     *
     * @param string $sql
     * @param array|null $params
     * @return int
     */
    public static function execute(string $sql, array $params = null): int
    {
        return self::query($sql, $params)->rowCount();
    }

    /**
     * Добавляет параметры в выражение с указанием типа
     *
     * Функция проходит по массиву с параметрами и добавляет их
     * в PDOStatement используя метод {@see PDOStatement::bindValue()}, при
     * этом указывается тип параметра (PDO::PARAM_*), который определяется по PHP-типу
     * значения
     *
     * @param PDOStatement $stmt
     * @param array|null $params
     */
    private static function prepareParams(PDOStatement $stmt, ?array $params = null)
    {
        if (!$params) {
            return;
        }

        foreach ($params as $param => $value) {
            if (is_int($param)) {
                $param++;
            }

            switch (gettype($value)) {
                case 'integer':
                    $stmt->bindValue($param, (int) $value, PDO::PARAM_INT);
                    break;
                case 'boolean':
                    $stmt->bindValue($param, $value ? 1 : 0, PDO::PARAM_BOOL);
                    break;
                case 'double':
                    $stmt->bindValue($param, (string) $value, PDO::PARAM_STR);
                    break;
                case 'string':
                    $stmt->bindValue($param, (string) $value, PDO::PARAM_STR);
                    break;
                case 'NULL':
                    $stmt->bindValue($param, $value, PDO::PARAM_NULL);
                    break;
                default:
                    throw new InvalidArgumentException(
                        "Неверный тип у аргумента $param в SQL запросе"
                    );
            }
        }
    }
}
