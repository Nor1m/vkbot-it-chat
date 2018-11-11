<?php

namespace App\commands;

use App\base\ApiController;
use App\base\BaseCommand;
use App\base\Message;

/**
 * Команда выводит список администраторов беседы
 * @package App\commands
 */
class AdminsCommand extends BaseCommand
{

    /**
     * @param array $argc
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    public function run(array $argc): void
    {
        $admins = ApiController::getChatAdmins($this->object()['peer_id']);
        $output = PHP_EOL;

        if (!empty($admins['profiles'])) {
            $output .= 'Люди:' . PHP_EOL . implode(PHP_EOL, array_map(
                    function ($admin) {
                        return "[id{$admin['id']}|{$admin['first_name']} {$admin['last_name']}]";
                    },
                    ApiController::getChatAdmins($this->object()['peer_id'])['profiles']
                ));
        }

        if (!empty($admins['groups'])) {
            $output .= PHP_EOL . 'Не люди: ' . PHP_EOL . implode(PHP_EOL, array_map(
                    function ($admin_grp) {
                        return "[club{$admin_grp['id']}|{$admin_grp['name']}]";
                    },
                    ApiController::getChatAdmins($this->object()['peer_id'])['groups']
                ));
        }

        Message::write($this->object()['peer_id'], 'message.admins', [
            '{admins}' => $output,
        ]);
    }
}