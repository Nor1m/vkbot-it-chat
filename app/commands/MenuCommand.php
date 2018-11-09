<?php

namespace App\commands;


use App\base\BaseCommand;

/**
 * Класс MenuCommand
 * @package App\commands
 */
class MenuCommand extends BaseCommand
{
    /**
     * @param $object array
     * @param $user array
     *
     * @param array $argc
     *
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    public function run(array $object, array $user, array $argc): void
    {
        $this->vk()->messages()->send(VK_TOKEN, array(
            'peer_id' => $object['peer_id'],
            'message' => "Доступные команды:\n-" . implode("\n-", AVAILABLE_CMDS) . ".",
        ));
    }
}