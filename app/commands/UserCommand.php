<?php

namespace App\commands;

use App\base\BaseCommand;
use App\base\Message;
use App\Log;
use App\models\Tech;
use App\models\TechProposed;
use App\models\User;

class UserCommand extends BaseCommand
{
    const FLAG_NAME = 'name';
    const FLAG_SURNAME = 'surname';
    const FLAG_PATR = 'patr';
    const FLAG_INFO = 'info';
    const FLAG_TECH = 'tech';
    const FLAG_DEL_TECH = 'del-tech';

    /**
     * @param array $argc
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    public function run(array $argc): void
    {
        $flag = reset($argc);

        $user = User::get($this->fromUser()['id']);

        Log::dump($user);

        if ($user === null) {
            Log::write("Сохранение нового пользователя (id {$this->fromUser()['id']})");
            if (User::create($this->fromUser())) {
                $user = User::get($this->fromUser()['id']);
            } else {
                Log::warning("Сохранение не удалось");
                return;
            }
        }

        switch ($flag) {
            case self::FLAG_SURNAME:
                $user->setSurname($argc[1]);
                break;
            case self::FLAG_NAME:
                $user->setName($argc[1]);
                break;
            case self::FLAG_PATR:
                $user->setPatr($argc[1]);
                break;

            case self::FLAG_TECH:

                $tech = Tech::getByCode($argc[1]);

                if ($tech === null) {
                    $proposal = TechProposed::getByCode($argc[1]);

                    if ($proposal === null) {
                        $user->addStackProposal(TechProposed::create($argc[1]));
                    } elseif (!$proposal->closed) {
                        $user->addStackProposal($proposal->id);
                    } else {
                        Message::write(
                            $this->object()['peer_id'],
                            "Такой технологии нет, и добавлять я её не собираюсь"
                        );
                        return;
                    }

                    Message::write(
                        $this->object()['peer_id'],
                        'Такой технологии нет, но я её запомню. Если позже её утвердят, она у тебя появится'
                    );
                }

                $user->addStackItem($tech->id);

                break;

            case self::FLAG_DEL_TECH:

                if (is_numeric($argc[1])) {
                    $user->removeStackItemByOrd($argc[1]);
                    break;
                }

                $tech = Tech::getByCode($argc[1]);

                if ($tech === null) {
                    Message::write($this->object()['peer_id'], "Такой технологи я не знаю, соре");
                    return;
                }

                $user->removeStackItem($tech->id);

                break;

            case self::FLAG_INFO:
            default:

                $msg = "Вот что я знаю о тебе:" . PHP_EOL
                    . 'Фамилия: ' . $user->last_name . PHP_EOL
                    . 'Имя: ' . $user->first_name;

                if ($user->patronymic) {
                    $msg .= PHP_EOL . 'Отчество: ' . $user->patronymic;
                }

                $user->loadStack();

                Log::dump($user->stack);

                if ($user->stack) {
                    $msg .= PHP_EOL . PHP_EOL
                        . 'Стак:' . PHP_EOL
                        . implode(PHP_EOL, array_map(
                            function (Tech $tech) {
                                return $tech->ord . '. ' . ($tech->name ?: $tech->code);
                            },
                            $user->stack
                        ));
                }

                Message::write($this->object()['peer_id'], $msg);

                return;
        }

        Message::write($this->object()['peer_id'], "Сделано");
    }
}