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
     * @param bool $closed
     * @param int $page
     * @param int $pageSize
     * @return self[]
     */
    public static function getPaged(bool $closed = false, int $page = 1, int $pageSize = 10)
    {
        $stmt = Db::pdo()->prepare(<<<SQL
SELECT *
FROM `tech_proposed`
WHERE closed = :closed
LIMIT :limit
OFFSET :offset
SQL
        );

        $stmt->bindValue('closed', $closed ? 1 : 0, PDO::PARAM_BOOL);
        $stmt->bindValue('limit', $pageSize, PDO::PARAM_INT);
        $stmt->bindValue('offset', ($page - 1) * $pageSize, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public static function getTotalCount($closed = false) {
        return Db::queryScalar("SELECT COUNT(id) FROM tech_proposed WHERE closed = :closed", [
            'closed' => $closed,
        ]);
    }

    public static function getDeniedCount() {
        return Db::queryScalar(<<<SQL
SELECT COUNT(proposed.id) as count
FROM tech_proposed proposed
  LEFT OUTER JOIN tech ON tech.code = proposed.code
WHERE proposed.closed = TRUE AND tech.id IS NULL
SQL
        );
    }

    public static function getAppliedCount() {
        return Db::queryScalar(<<<SQL
SELECT COUNT(proposed.id) as count
FROM tech_proposed proposed
  INNER JOIN tech ON tech.code = proposed.code
SQL
        );
    }

    public static function getDenied($page = 1, $pageSize = 10): array
    {
        return Db::queryAll(<<<SQL
SELECT proposed.*
FROM tech_proposed proposed
  LEFT OUTER JOIN tech ON tech.code = proposed.code
WHERE proposed.closed = TRUE AND tech.id IS NULL
LIMIT :lim
OFFSET :offs
SQL
            , [
                'lim' => $pageSize,
                'offs' => ($page - 1) * $pageSize,
            ],
            PDO::FETCH_CLASS,
            Tech::class
        );
    }

    public static function getApplied($page = 1, $pageSize = 10): array
    {
        return Db::queryAll(<<<SQL
SELECT tech.*
FROM tech_proposed proposed
  INNER JOIN tech ON tech.code = proposed.code
LIMIT :lim
OFFSET :offs
SQL
            , [
                'lim' => $pageSize,
                'offs' => ($page - 1) * $pageSize,
            ],
            PDO::FETCH_CLASS,
            Tech::class
        );
    }

    public function isApplied():bool
    {
        return Db::queryScalar("SELECT 1 FROM tech WHERE code = :code", [
            'code' => $this->code,
        ]);
    }

    public function delete(): bool
    {
        return Db::execute("DELETE FROM tech_proposed WHERE id = :id", [
            'id' => $this->id,
        ]);
    }

    public function close(): bool
    {
        $success = Db::execute(
            "UPDATE tech_proposed SET closed = TRUE WHERE id = :id",
            ['id' => $this->id]
        );
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