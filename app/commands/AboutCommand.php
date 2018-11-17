<?php

namespace App\commands;


use App\base\BaseCommand;

/**
 * Класс AboutCommand
 * @package App\commands
 */
class AboutCommand extends BaseCommand
{
    /**
     * @param array $argc
     *
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    public function run(array $argc): void
    {
        $this->vk()->messages()->send(VK_TOKEN, array(
            'peer_id' => $this->object()['peer_id'],
            'message' =>
                 "Название: " . BOT_INFO['name'] . ".\n"
                ."Версия: " . BOT_INFO['version'] . ".\n"
                ."Описание: " . BOT_INFO['description'] . ".\n"
                ."Ссылка: " . BOT_INFO['link']
        ));
    }
}
