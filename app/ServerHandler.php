<?php

namespace App;


use App\base\BaseCommand;
use App\base\Config;
use App\base\Message;
use App\base\Protect;
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

    /** @var array */
    private $_fromUser;

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
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    public function messageNew(int $group_id, ?string $secret, array $object)
    {
        Protect::init($object);
        Protect::ensureGroupSecret($group_id, $secret);

        $this->_fromUser = $this->_vk->users()->get(VK_TOKEN, array(
            'user_ids' => $object['from_id'],
        ))[0];

        if (isset($object['action'])) {
            $this->handleAction($object);
        }

        $text = trim($object['text']);

        if (strtok($text, ' ') === BOT_ANCHOR) {
            $this->runCommand(preg_split('/\\s+/', $text, -1, PREG_SPLIT_NO_EMPTY), $object);
        }

        $this->end();
    }

    /**
     * @param $argc
     * @param $object
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    protected function runCommand($argc, $object)
    {
        $cmd = mb_strtolower($argc[1]);

        $cmdInfo = Config::getCommand($cmd);

        if ($cmdInfo === null) {
            Message::write($object['peer_id'], Message::t('warning.wrong_cmd', array(
                '{$}'   => BOT_ANCHOR,
                '{cmd}' => $cmd,
            )));
            $this->end();
        }

        $cmdClass = $cmdInfo['class'];

        if (class_exists($cmdClass)) {
            /** @var BaseCommand $cmdObj */
            $cmdObj = new $cmdClass($this->_vk, $object, $this->_fromUser, $cmdInfo);

            $cmdObj->process(array_slice($argc, 2));
        }
    }

    /**
     * @param $object
     *
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     * @throws \Exception
     */
    protected function handleAction($object)
    {
        switch ($object['action']->type) {
            case "chat_invite_user":
            case "chat_invite_user_by_link":

                // узнаем имя юзера
                $user = $this->_vk->users()->get(VK_TOKEN, array(
                    'user_ids' => $object['action']->member_id,
                ))[0];

                // отправляем сообщение в беседу
                Message::write($object['peer_id'], Message::t('message.greeting', array(
                    '{name}'    => $user['first_name'],
                    '{surname}' => $user['last_name'],
                )));

                $this->end();
                break;

            case "chat_kick_user":

                if ($this->_fromUser['id'] != $object['action']->member_id) {
                    // его кикнул кто-то другой, неинтересно
                    $this->end();
                    break;
                }

                // он сам вышел
                $this->_vk->messages()->send(VK_TOKEN, array(
                    'peer_id' => $object['peer_id'],
                    'attachment' => Config::attachment('leave'),
                ));

                $this->end();
                break;
        }
    }

    /**
     * @param $event
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    public function parse($event)
    {
        Log::write("Получен ивент типа $event->type");
        if ($event->type === 'message_edit') {
            $this->messageNew($event->group_id, $event->secret, (array) $event->object);
            return;
        }
        parent::parse($event);
    }

    private function end()
    {
        echo 'ok';
        exit();
    }
}
