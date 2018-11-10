<?php

namespace App\commands;

use App\base\BaseCommand;
use App\base\Config;

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
        if (!$argc) {
            return;
        }

        $object = $this->object();

        $chat_id  = $object['peer_id'] - 2000000000;
        $flag_gif = true;

        foreach ($argc as $key => $value) {
            if (preg_match('~\[id(.\d+)\|~', $value, $matches, PREG_OFFSET_CAPTURE)) {

                // если несколько юзеров то гифку показываем только раз
                if ($flag_gif) {
                    // отправляем юзеру гифку
                    $this->vk()->messages()->send(VK_TOKEN, array(
                        'chat_id'    => $chat_id,
                        'peer_id'    => $object['peer_id'],
                        'attachment' => Config::attachment('kick'),
                    ));
                    $flag_gif = false;
                }

                // исключаем юзера из беседы
                $this->vk()->messages()->removeChatUser(VK_TOKEN, array(
                    'chat_id' => $chat_id,
                    'user_id' => $matches[1][0],
                ));
            }
        }
    }
}