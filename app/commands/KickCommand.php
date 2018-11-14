<?php

namespace App\commands;

use App\base\ApiController;
use App\base\BaseCommand;
use App\base\Config;
use App\base\Message;
use App\base\Protect;
use App\Log;

/**
 * Класс KickCommand
 * @package App\commands
 */
class KickCommand extends BaseCommand
{
    /**
     * @param array $argc
     *
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     * @throws \Exception
     */
    public function run(array $argc): void
    {
        $object = $this->object();

        // если есть fwd_messages то ид для кика берем оттуда
        if (!empty($object['fwd_messages'])) {

            $users_to_kick_id = array_unique(array_map(function ($obj) {
                return $obj->from_id;
            }, $object['fwd_messages']));
            $this->kickUser($users_to_kick_id, $object['peer_id']);

        }  else if (!empty($argc)) { // иначе берем из аргументов

            $users_to_kick_id = array_unique(array_map(function ($val) {
                return $this->getUserIdOnArg($val);
            }, $argc));
            $this->kickUser($users_to_kick_id, $object['peer_id']);

        }
    }

    /**
     * @param string $value
     * @return string
     */
    public function getUserIdOnArg(string $value)
    {
        if (preg_match('~\[id(.\d+)\|~', $value, $matches, PREG_OFFSET_CAPTURE)) {
            return $matches[1][0];
        }
    }

    /**
     * @param $users_to_kick_id
     * @param $peer_id
     */
    public function kickUser($users_to_kick_id, $peer_id): void
    {
        $flag_gif = false;
        $chat_id = $peer_id - 2000000000;

        foreach ($users_to_kick_id as $key => $user_id) {

            if (!Protect::isChatMember($user_id, $peer_id)) {
                Message::write($peer_id, Message::t('warning.user_not_in_chat'));
                return;
            }

            // если это админ беседы
            if (Protect::isChatAdmin($user_id, $peer_id)) {
                Message::write($peer_id, Message::t('warning.not_kick_admin'));
                return;
            }

            if (!$flag_gif) {
                // отправляем юзеру гифку
                $this->vk()->messages()->send(VK_TOKEN, array(
                    'chat_id' => $chat_id,
                    'peer_id' => $peer_id,
                    'attachment' => Config::attachment('kick'),
                ));
                $flag_gif = true;
            }

            // исключаем юзера из беседы
            $this->vk()->messages()->removeChatUser(VK_TOKEN, array(
                'chat_id' => $chat_id,
                'user_id' => $user_id,
            ));
        }
    }
}