<?php

namespace App\commands;


use App\base\BaseCommand;

/**
 * Класс HelloCommand
 * @package App\commands
 */
class HelloCommand extends BaseCommand
{
    /**
     * @param array $argc
     *
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    public function run(array $argc): void
    {
        $user = $this->fromUser();
        $this->vk()->messages()->send(VK_TOKEN, array(
            'peer_id' => $this->object()['peer_id'],
            'message' => "{$user['first_name']} {$user['last_name']}, привет. Аргументы: "
                . implode(', ', $argc),
        ));
    }
}