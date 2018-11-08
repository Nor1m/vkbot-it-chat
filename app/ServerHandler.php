<?php

namespace App;


use App\base\BaseCommand;
use VK\CallbackApi\Server\VKCallbackApiServerHandler;
use VK\Client\VKApiClient;

/**
 * Класс ServerHandler
 * @package App
 */
class ServerHandler extends VKCallbackApiServerHandler
{

    /** @var VKApiClient */
    private $_vk;

    public function __construct(VKApiClient $vk)
    {
        $this->_vk = $vk;
    }

    /**
     * @param int $group_id
     * @param null|string $secret
     */
    function confirmation(int $group_id, ?string $secret)
    {
        Log::write('Обработка подтверждения');
        if ($secret === VK_SECRET_KEY && $group_id === VK_GROUP_ID) {
            Log::write('Обработка подтверждения: успешно');
            echo VK_CONFIRMATION_TOKEN;
        }
    }

    /**
     * @param int $group_id
     * @param null|string $secret
     * @param array $object
     *
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    public function messageNew(int $group_id, ?string $secret, array $object)
    {
        $text = trim($object['text']);

        if (strtok($text, ' ') !== '$') {
            $this->end();
        }

        $argc = explode(' ', $text);

        /** @var array $user_info */
        $user = $this->_vk->users()->get(VK_TOKEN, array(
            'user_ids' => $object['from_id'],
        ))[0];

        $cmd = $argc[1];

        if (!in_array($cmd, AVAILABLE_CMDS)) {
            $this->end();
        }

        $cmdClass = 'App\\commands\\' . ucfirst($cmd) . 'Command';

        /** @var BaseCommand $cmdObj */
        $cmdObj = new $cmdClass($this->_vk);

        $cmdObj->run($object, $user, array_slice($argc, 2));

        $this->end();
    }

    private function end()
    {
        echo 'ok';
        exit();
    }
}
