<?php

namespace App\models;

use App\base\Db;
use PDO;

class Tech
{
    public $id;
    public $code;
    public $name;
    public $description;

    /**
     * @var int Порядковый номер. Выбирается только во время получения стака юзера
     */
    public $ord;

    public static function getByCode($code): ?self
    {
        $stmt = Db::pdo()->prepare(<<<SQL
SELECT * FROM tech WHERE code = :code
SQL
        );

        $stmt->execute(array(
            'code' => $code,
        ));

        if ($stmt->rowCount() < 1) {
            return null;
        }

        return $stmt->fetchObject(self::class);
    }

    public static function create($code): ?int
    {
        $stmt = Db::pdo()->prepare(<<<SQL
INSERT INTO tech (code, name, description)
VALUES (:code, '', '')
SQL
        );

        $stmt->execute([
            'code' => $code,
        ]);

        if ($stmt->rowCount() < 1) {
            return null;
        }

        return Db::pdo()->lastInsertId();
    }

    /**
     * @param int $limit
     * @return self[]
     */
    public static function findAll(int $limit = 10)
    {
        $stmt = Db::pdo()->prepare(<<<SQL
SELECT *
FROM `tech`
LIMIT :limit
SQL
        );

        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }
}