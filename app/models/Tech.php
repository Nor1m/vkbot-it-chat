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
     * @return self[]
     */
    public static function findAll()
    {
        $stmt = Db::pdo()->prepare(<<<SQL
SELECT *
FROM `tech`
SQL
        );

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    /**
     * @param string $code
     * @return bool
     */
    public function updateCode(string $code): bool
    {
        return Db::execute(<<<SQL
UPDATE tech SET code = :code WHERE id = :id
SQL
            , [
                'code' => $code,
                'id'   => $this->id,
            ]
        );
    }

    /**
     * @param string $name
     * @return bool
     */
    public function updateName(string $name): bool
    {
        return Db::execute(<<<SQL
UPDATE tech SET name = :name WHERE id = :id
SQL
            , [
                'name' => $name,
                'id'   => $this->id,
            ]
        );
    }

    /**
     * @param string $description
     * @return bool
     */
    public function updateDescription(string $description): bool
    {
        return Db::execute(<<<SQL
UPDATE tech SET description = :description WHERE id = :id
SQL
            , [
                'description' => $description,
                'id'          => $this->id,
            ]
        );
    }
}