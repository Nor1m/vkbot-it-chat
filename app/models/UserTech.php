<?php

namespace App\models;

use App\base\Db;

class UserTech
{
    public $user_id;
    public $tech_id;
    public $ord;

    public static function get($userId, $techId): ?self
    {
        return Db::queryObject(<<<SQL
SELECT *
FROM user_tech
WHERE user_id = :userId
  AND tech_id = :techId
SQL
            , [
                'userId' => $userId,
                'techId' => $techId,
            ]
        );
    }

    public static function create($userId, $techId): bool
    {
        return Db::execute(<<<SQL
INSERT INTO user_tech (user_id, tech_id, ord)
VALUES (:userId, :techId, (
  SELECT coalesce(max(ord) + 1, 1)
  FROM user_tech us
  WHERE user_id = :userId
))
SQL
            , [
                'userId' => $userId,
                'techId' => $techId,
            ]
        );
    }

    public static function getTechByOrd($userId, $ord): Tech
    {
        return Db::queryObject(<<<SQL
SELECT tech.*, ut.ord
FROM user_tech ut 
  INNER JOIN tech tech ON ut.tech_id = tech.id
WHERE ut.ord = :ord AND ut.user_id = :userId
SQL
            , [
                'ord'    => $ord,
                'userId' => $userId,
            ],
            Tech::class
        );
    }

    public static function moveTech(int $userId, int $techId, int $pos): bool
    {
        // если юзер введёт 999 то по факту техология улетит в конец
        $least_ord = (int) Db::queryScalar(<<<SQL
SELECT least(coalesce(max(ord), 1), :ord) AS ord
FROM user_tech
WHERE user_id = :userId
SQL
            , [
                'userId' => $userId,
                'ord'    => $pos,
            ]
        );

        return Db::execute(<<<SQL
UPDATE user_tech ut
  INNER JOIN user_tech ut2
    ON ut.user_id = ut2.user_id
      AND ut.tech_id <> ut2.tech_id
SET ut.ord = :ord,
    ut2.ord = (
    SELECT
      CASE
        WHEN ut2.ord >= :ord AND ut2.ord < ut.ord
          THEN ut2.ord + 1
        WHEN ut2.ord <= :ord AND ut2.ord > ut.ord
          THEN ut2.ord - 1
        ELSE
          ut2.ord
        END
    )
WHERE ut.user_id = :userId AND ut.tech_id = :techId
SQL
            , [
                'techId' => $techId,
                'userId' => $userId,
                'ord'    => $least_ord,
            ]
        );
    }

    public static function moveTechEnd(int $userId, int $techId): bool
    {
        // если юзер введёт 999 то по факту техология улетит в конец
        $last_ord = (int) Db::queryScalar(<<<SQL
SELECT coalesce(max(ord), 1) AS ord
FROM user_tech
WHERE user_id = :userId
SQL
            , ['userId' => $userId]
        );

        return Db::execute(<<<SQL
UPDATE user_tech ut
  INNER JOIN user_tech ut2
    ON ut.user_id = ut2.user_id
      AND ut.tech_id <> ut2.tech_id
SET ut.ord = :ord,
    ut2.ord = (
      SELECT CASE
        WHEN ut2.ord >= :ord AND ut2.ord < ut.ord
          THEN ut2.ord + 1
        WHEN ut2.ord <= :ord AND ut2.ord > ut.ord
          THEN ut2.ord - 1
        ELSE
          ut2.ord
        END
    )
WHERE ut.user_id = :userId AND ut.tech_id = :techId
SQL
            , [
                'techId' => $techId,
                'userId' => $userId,
                'ord'    => $last_ord,
            ]
        );
    }

    public static function getTechByCode(int $userId, $code)
    {
        return Db::queryObject(<<<SQL
SELECT tech.*, ut.ord
FROM user_tech ut 
  INNER JOIN tech tech ON ut.tech_id = tech.id AND tech.code = :code
WHERE ut.user_id = :userId
SQL
            , [
                'code'   => $code,
                'userId' => $userId,
            ],
            Tech::class
        );
    }

    public static function sortTechsAlphabetical($userId)
    {
        return Db::execute(<<<SQL
UPDATE user_tech ut
  -- инициализация счётчика, должна быть только тут,
  -- иначе не работает
  CROSS JOIN (SELECT @x := 0) r

  INNER JOIN (
    SELECT (@x := @x + 1) as row_num, t2.*
    FROM tech
      INNER JOIN user_tech t2
        ON tech.id = t2.tech_id AND t2.user_id =:userId
    ORDER BY CONCAT(tech.name, tech.code)
  ) t ON ut.tech_id = t.tech_id AND ut.user_id = t.user_id
SET ut.ord = t.row_num
SQL
            , ['userId' => $userId]
        );
    }
}