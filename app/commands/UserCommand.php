<?php

namespace App\commands;

use App\commands\user\Tech as TechCommand;
use App\base\BaseCommand;
use App\base\Message;
use App\Log;
use App\models\Tech;
use App\models\TechProposed;
use App\models\User;
use App\models\UserTech;

class UserCommand extends BaseCommand
{
    const FLAG_NAME = 'name';
    const FLAG_SURNAME = 'surname';
    const FLAG_PATR = 'patr';
    const FLAG_INFO = 'info';
    const FLAG_TECH = 'tech';

    /**
     * @param array $argc
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     * @throws \Exception
     */
    public function run(array $argc): void
    {
        $flag = reset($argc);

        if ($flag == self::FLAG_TECH) {
            array_shift($argc);
            $cmd = new TechCommand($this->vk(), $this->object(), $this->fromUser(), []);
            $cmd->run($argc);
            return;
        }

        if (!$user = User::getOrCreate($this->fromUser())) {
            Message::write(
                $this->object()['peer_id'],
                Message::t('error.smf_wrong')
            );
            return;
        }

        switch ($flag) {
            case self::FLAG_SURNAME:
                $user->updateSurname($argc[1]);
                break;
            case self::FLAG_NAME:
                $user->updateName($argc[1]);
                break;
            case self::FLAG_PATR:
                $user->updatePatr($argc[1]);
                break;

            case self::FLAG_INFO:
            default:

                $msg = Message::t('message.user_info', [
                    '{name}' => $user->first_name,
                    '{surname}' => $user->last_name,
                ]);

                if ($user->patronymic) {
                    $msg .= PHP_EOL
                        . Message::t('message.user_info_patr', ['{patr}' => $user->patronymic]);
                }

                $user->loadStack();

                Log::dump($user->stack);

                if ($user->stack) {
                    $msg .= PHP_EOL . PHP_EOL
                        . Message::t('message.user_stack_head')
                        . implode(PHP_EOL, array_map(
                            function (Tech $tech) {
                                return Message::t('message.tech_item', [
                                    '{ord}' => $tech->ord,
                                    '{name}' => ($tech->name ?: $tech->code),
                                ]);
                            },
                            $user->stack
                        ));
                }

                Message::write($this->object()['peer_id'], $msg);

                return;
        }

        Message::write($this->object()['peer_id'], Message::t('success.done'));
    }
}