<?php

namespace App\base;


use VK\Client\VKApiClient;

/**
 * Класс BaseCommand
 * @package App\base
 */
abstract class BaseCommand
{
    const ACCESS_ANYONE        = 0;
    const ACCESS_GROUP_MEMBERS = 2;
    const ACCESS_GROUP_ADMINS  = 4;
    const ACCESS_CHAT_MEMBERS  = 8;
    const ACCESS_CHAT_ADMINS   = 16;

    private $_vk;

    private $_fromUser;

    private $_object;

    public $_access;

    public $_description;

    public $_class;

    public $_aliases;

    public function __construct(VKApiClient $vk, array $object, array $fromUser, array $config)
    {
        $this->_vk       = $vk;
        $this->_object   = $object;
        $this->_fromUser = $fromUser;

        foreach ($config as $field => $value) {
            $field        = '_' . $field;
            $this->$field = $value;
        }
    }

    protected function vk(): VKApiClient
    {
        return $this->_vk;
    }

    protected function fromUser(): array
    {
        return $this->_fromUser;
    }

    protected function object(): array
    {
        return $this->_object;
    }

    public abstract function run(array $argc): void;

    /**
     * Начать запуск команды
     *
     * Запускает проверку доступа, какие-нибудь промежуточные действия,
     * метод {@see run()}.
     *
     * @param array $argc
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    public function process(array $argc): void
    {
        $this->checkAccess();

        if (reset($argc) === '-h' || reset($argc) === '-help') {
            $this->processHelpFlag();
            return;
        }

        if (reset($argc) === '-alt' || reset($argc) === '-a') {
            $this->processAltFlag();
            return;
        }

        $this->run($argc);
    }

    /**
     * Выводит сообщение с описанием команды
     *
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    private function processHelpFlag(): void
    {
        $this->vk()->messages()->send(VK_TOKEN, array(
            'message' => $this->_description,
            'peer_id' => $this->object()['peer_id'],
        ));
    }

    private function processAltFlag(): void
    {
        Message::write(
            $this->object()['peer_id'],
            'Алиасы команды:'
                . PHP_EOL
                . implode(PHP_EOL, array_map(function ($alt) {
                    return '$ ' . $alt;
                }, $this->_aliases))
        );
    }


    /**
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    public function checkAccess()
    {
        if (self::ACCESS_GROUP_MEMBERS & $this->_access) {
            Protect::checkIsGroupMember($this->fromUser(), VK_GROUP_ID);
        }

        if (self::ACCESS_GROUP_ADMINS & $this->_access) {
            Protect::checkIsGroupAdmin($this->fromUser(), VK_GROUP_ID);
        }

        if (self::ACCESS_CHAT_MEMBERS & $this->_access) {
            Protect::checkIsChat($this->object()['peer_id']);
            Protect::checkIsChatMember($this->fromUser(), $this->object()['peer_id']);
        }

        if (self::ACCESS_CHAT_ADMINS & $this->_access) {
            Protect::checkIsChat($this->object()['peer_id']);
            Protect::checkIsChatAdmin($this->fromUser(), $this->object()['peer_id']);
        }
    }
}