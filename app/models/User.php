<?php

namespace App\models;

use App\base\Db;
use App\Log;
use PDO;

class User
{
    /**
     * @var int
     */
    public $vk_id;

    /**
     * @var string
     */
    public $first_name;

    /**
     * @var string
     */
    public $last_name;

    /**
     * @var string
     */
    public $patronymic;

    public $stack;

    /**
     * @param int $vkId
     * @return null|self
     */
    public static function get(int $vkId): ?self
    {
        $st = Db::pdo()->prepare("SELECT * FROM `user` WHERE vk_id = :id");

        $st->bindValue('id', $vkId, PDO::PARAM_INT);

        $st->execute();

        if ($st->rowCount() < 1) {
            return null;
        }

        return $st->fetchObject(self::class);
    }

    /**
     * @param array $user
     * @return bool
     */
    public static function create(array $user): bool
    {
        $st = Db::pdo()->prepare(<<<SQL
INSERT INTO `user` (vk_id, first_name, last_name)
VALUES (:id, :fname, :lname)
SQL
        );

        return $st->execute([
            ':id' => $user['id'],
            ':fname' => $user['first_name'],
            ':lname' => $user['last_name'],
        ]);
    }

    public static function getOrCreate(array $user): ?self
    {
        $user = User::get($user['id']);

        Log::dump($user);

        if ($user === null) {
            Log::write("Сохранение нового пользователя (id {$user['id']})");
            if (User::create($user)) {
                return User::get($user['id']);
            } else {
                Log::warning("Сохранение не удалось");
                return null;
            }
        }

        return $user;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function updateName(string $name): bool
    {
        return Db::pdo()->prepare(<<<SQL
UPDATE `user`
SET first_name = :fname
WHERE vk_id = :id
SQL
        )->execute([
            'fname' => $name,
            'id'    => $this->vk_id,
        ]);
    }

    /**
     * @param string $surname
     * @return bool
     */
    public function updateSurname(string $surname): bool
    {
        return Db::pdo()->prepare(<<<SQL
UPDATE `user`
SET last_name = :fname
WHERE vk_id = :id
SQL
        )->execute([
            'fname' => $surname,
            'id'    => $this->vk_id,
        ]);
    }

    /**
     * @param string $patr
     * @return bool
     */
    public function updatePatr(string $patr): bool
    {
        return Db::pdo()->prepare(<<<SQL
UPDATE `user`
SET patronymic = :fname
WHERE vk_id = :id
SQL
        )->execute([
            'fname' => $patr,
            'id'    => $this->vk_id,
        ]);
    }

    public function addStackItem(int $itemId): bool
    {
        return Db::pdo()->prepare(<<<SQL
INSERT INTO user_tech (user_id, tech_id, ord)
VALUES (
  :userId,
  :itemId,
  (SELECT coalesce(max(ord) + 1, 1) FROM user_tech us WHERE user_id = :userId)
)
ON DUPLICATE KEY UPDATE user_id = user_id
SQL
        )->execute([
            'userId' => $this->vk_id,
            'itemId' => $itemId,
        ]);
    }

    /**
     * @param int $proposalId
     * @return bool
     */
    public function addStackProposal(int $proposalId): bool
    {
        return Db::pdo()->prepare(<<<SQL
INSERT INTO user_proposed (user_id, proposed_id)
VALUES (:userId, :proposalId)
ON DUPLICATE KEY UPDATE user_id = user_id
SQL
        )->execute([
            'userId'     => $this->vk_id,
            'proposalId' => $proposalId,
        ]);
    }

    /**
     * @return void
     */
    public function loadStack(): void
    {
        $stmt = Db::pdo()->prepare(<<<SQL
SELECT stack.*, user_tech.ord
FROM tech stack
INNER JOIN user_tech ON stack.id = user_tech.tech_id AND user_tech.user_id = :userId
ORDER BY ord
SQL
        );

        $stmt->execute([
            'userId' => $this->vk_id,
        ]);

        $this->stack = $stmt->fetchAll(PDO::FETCH_CLASS, Tech::class);
    }

    /**
     * @param int $ord
     * @return bool
     */
    public function removeStackItemByOrd(int $ord): bool
    {
        $res = Db::pdo()->prepare(<<<SQL
DELETE FROM user_tech
WHERE ord = :ord AND user_id = :userId
SQL
        )->execute(array(
            'userId' => $this->vk_id,
            'ord' => $ord,
        ));

        if ($res) {
            $res = $res && Db::execute(
                    "UPDATE user_tech SET ord = ord - 1 WHERE user_id = :user AND ord > :ord",
                    [
                        'user' => $this->vk_id,
                        'ord'  => $ord,
                    ]
                );
        }

        return $res;
    }

    /**
     * @param int $itemId
     * @return bool
     */
    public function removeStackItem(int $itemId): bool
    {
        $ord = Db::queryScalar("SELECT ord FROM user_tech WHERE user_id = ? AND tech_id = ?", [
            $this->vk_id,
            $itemId,
        ]);

        return $this->removeStackItemByOrd($ord);
    }
}