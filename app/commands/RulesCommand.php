<?php

namespace App\commands;


use App\base\BaseCommand;
use App\base\Message;

/**
 * Класс RulesCommand
 * @package App\commands
 */
class RulesCommand extends BaseCommand
{
    /**
     * @param array $argc
     *
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    public function run(array $argc): void
    {
        Message::write($this->object()['peer_id'], 'message.rules');
    }
}