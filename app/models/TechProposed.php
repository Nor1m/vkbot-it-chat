<?php
/**
 * Created by PhpStorm.
 * User: ROMAN
 * Date: 18.11.2018
 * Time: 12:16
 */

namespace App\models;


use App\base\Db;
use PDO;

class TechProposed
{
    public $id;
    public $code;
    public $closed;

    /**
     * @param string $code
     * @return TechProposed|null
     */
    public static function getByCode(string $code): ?self
    {
        $stmt = Db::pdo()->prepare("SELECT * FROM `tech_proposed` WHERE code = :code");
        $stmt->execute([
            'code' => $code,
        ]);

        if ($stmt->rowCount() < 1) {
            return null;
        }

        return $stmt->fetchObject(self::class);
    }

    /**
     * @param int $id
     * @return TechProposed|null
     */
    public static function get(int $id): ?self
    {
        $stmt = Db::pdo()->prepare("SELECT * FROM `tech_proposed` WHERE id = :id");
        $stmt->execute([
            'id' => $id,
        ]);

        if ($stmt->rowCount() < 1) {
            return null;
        }

        return $stmt->fetchObject(self::class);
    }

    /**
     * @param string $code
     * @return int|null
     */
    public static function create(string $code): ?int
    {
        $st = Db::pdo()->prepare(<<<SQL
INSERT INTO `tech_proposed` (code)
VALUES (:code)
SQL
        );

        $success = $st->execute([
            'code' => $code,
        ]);

        if ($success) {
            return Db::pdo()->lastInsertId();
        }

        return null;
    }

    /**
     * @param int $limit
     * @param bool $closed
     * @return self[]
     */
    public static function findAll(int $limit = 10, bool $closed = false)
    {
        $stmt = Db::pdo()->prepare(<<<SQL
SELECT *
FROM `tech_proposed`
WHERE closed = :closed
LIMIT :limit
SQL
        );

        $stmt->bindValue('closed', $closed ? 1 : 0, PDO::PARAM_BOOL);
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public function close(): bool
    {
        $success = Db::pdo()->prepare("UPDATE tech_proposed SET closed = TRUE")->execute();
        if ($success) {
            $this->closed = true;
        }
        return $success;
    }

    public function apply(): bool
    {
        Db::pdo()->inTransaction() or Db::pdo()->beginTransaction();

        $tech_id = Tech::create($this->code);
        $this->close();

        $users_st = Db::pdo()->prepare(<<<SQL
SELECT `user`.vk_id
FROM `user`
  INNER JOIN user_proposed proposed
    ON proposed.user_id = `user`.vk_id
      AND proposed.proposed_id = :proposedId
SQL
        );

        $users_st->execute([
            'proposedId' => $this->id,
        ]);

        $proposed_users = $users_st->fetchAll(PDO::FETCH_COLUMN);

        $values = [];
        $query_params = [
            'techId' => $tech_id,
        ];
        $c = 0;
        foreach ($proposed_users as $user_id) {
            $c++;
            $query_params['userId' . $c] = $user_id;
            $values[] = <<<VALUE
(
  :userId$c,
  :techId,
  (
    SELECT coalesce(max(ord) + $c, $c)
    FROM user_tech us
    WHERE user_id = :userId$c
  )
)
VALUE;
        }

        $insert_user_techs = Db::pdo()->prepare(
            'INSERT INTO user_tech (user_id, tech_id, ord) VALUES '
                . implode(', ', $values)
        )->execute($query_params);

        Db::pdo()->commit();
        return true;
    }
}