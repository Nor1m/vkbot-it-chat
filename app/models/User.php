<?php

namespace App\models;

use App\base\Db;
use App\Log;
use PDO;

class User
{
    public static function get(int $vk_id)
    {
        $st = Db::pdo()->prepare("SELECT * FROM `user` WHERE vk_id = :id");

        Log::write($vk_id);
        Log::write($st->queryString);

        $st->execute(array(
            ':id' => $vk_id,
        ));

        return $st->fetchObject();
    }

    public static function create(array $user)
    {
        $st = Db::pdo()->prepare(<<<SQL
INSERT INTO `user` (vk_id, first_name, last_name)
VALUES (:id, :fname, :lname)
SQL
        );

        $st->execute(array(
            ':id' => $user['id'],
            ':fname' => $user['first_name'],
            ':lname' => $user['last_name'],
        ));
    }
}