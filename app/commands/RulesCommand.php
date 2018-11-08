<?php

namespace App\commands;


use App\base\BaseCommand;

/**
 * Класс RulesCommand
 * @package App\commands
 */
class RulesCommand extends BaseCommand
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
            'message' => VK_RULES,
        ));
    }
}