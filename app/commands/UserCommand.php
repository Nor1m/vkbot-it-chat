<?php

namespace App\commands;

use App\base\BaseCommand;
use App\base\Db;
use App\base\Message;
use App\Log;
use App\models\User;

class UserCommand extends BaseCommand
{
    const FLAG_NAME = '-name';
    const FLAG_SURNAME = '-surname';
    const FLAG_PATR = '-patr';

    public function run(array $argc): void
    {
        $flag = reset($argc);

        if (!in_array($flag, [self::FLAG_NAME, self::FLAG_PATR, self::FLAG_SURNAME], true)) {
            $user = User::get($this->fromUser()['id']);

            Log::write(json_encode($user));

            if ($user === false) {
                Log::write(json_encode(Db::pdo()->errorInfo()));
            }

            Message::write(
                $this->object()['peer_id'],
                "Вот что я знаю о тебе:" . PHP_EOL
                    . 'Имя: ' . $user->first_name . PHP_EOL
                    . 'Фамилия: ' . $user->last_name . PHP_EOL
                    .  ($user->patronymic ? 'Отчество: ' . $user->patronymic : '')
            );
            return;
        }
    }
}