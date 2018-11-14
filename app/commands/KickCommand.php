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
            foreach ($object['fwd_messages'] as $key => $value) {
                $user_to_kick_id = $value->from_id;
                $this->kickUser($user_to_kick_id, $object['peer_id']);
            }
            return;
        }

        // иначе берем из аргументов
        if ($argc) {
            foreach ($argc as $key => $value) {
                $user_to_kick_id = $this->getUserIdOnArg($value);
                $this->kickUser($user_to_kick_id, $object['peer_id']);
            }
        }
        return;
    }

    /**
     * @param string $value
     * @return string
     */
    public function getUserIdOnArg(string $value): string
    {
        if (preg_match('~\[id(.\d+)\|~', $value, $matches, PREG_OFFSET_CAPTURE)) {
            return $matches[1][0];
        }
    }

    public function kickUser($user_to_kick_id, $peer_id)
    {
        global $flag_gif;
        if (!$flag_gif) $flag_gif = false;

        $chat_id = $peer_id - 2000000000;

        if (!Protect::isChatMember($user_to_kick_id, $peer_id)) {
            Message::write($peer_id, Message::t('warning.user_not_in_chat'));
            return;
        }

        // если это админ беседы
        if (Protect::isChatAdmin($user_to_kick_id, $peer_id)) {
            Message::write($peer_id, Message::t('warning.not_kick_admin'));
            return;
        }

        if (!$flag_gif) {
            // отправляем юзеру гифку
            $this->vk()->messages()->send(VK_TOKEN, array(
                'chat_id'    => $chat_id,
                'peer_id'    => $peer_id,
                'attachment' => Config::attachment('kick'),
            ));
            $flag_gif = true;
        }

        // исключаем юзера из беседы
        $this->vk()->messages()->removeChatUser(VK_TOKEN, array(
            'chat_id' => $chat_id,
            'user_id' => $user_to_kick_id,
        ));
    }
}