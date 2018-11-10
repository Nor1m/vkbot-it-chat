<?php

namespace App\commands;


use App\base\BaseCommand;
use App\base\Config;
use App\base\Message;

/**
 * Класс MenuCommand
 * @package App\commands
 */
class MenuCommand extends BaseCommand
{
    /**
     * @param array $argc
     *
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    public function run(array $argc): void
    {
        Message::write($this->object()['peer_id'], 'message.menu', array(
            '{commands}' => "\n-" . implode("\n-", array_keys(Config::commands())) . ".",
        ));
    }
}