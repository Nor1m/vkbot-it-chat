<?php
/**
 * Created by PhpStorm.
 * User: ROMAN
 * Date: 09.11.2018
 * Time: 22:44
 */

namespace App\base;


class Protect
{
    private static $_object;

    public static function init($object)
    {
        self::$_object = $object;
    }

    public static function ensureGroupSecret($groupId, $secret): void
    {
        if ($groupId !== VK_GROUP_ID && $secret !== VK_SECRET_KEY) {
            exit;
        }
    }

    public static function end()
    {
        echo 'ok';
        exit;
    }

    public static function checkIsGroupMember(array $fromUser, int $groupId): void
    {
        return;
    }

    public static function checkIsGroupAdmin(array $fromUser, int $groupId): void
    {
        return;
    }

    public static function checkIsChatMember(array $fromUser, int $chatId): void
    {
        return;
    }

    /**
     * @param array $fromUser
     * @param int $peer_id
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    public static function checkIsChatAdmin(array $fromUser, int $peer_id): void
    {
        if (!self::isChatAdmin($fromUser['id'], $peer_id)) {
            Message::write(self::$_object['peer_id'], 'warning.not_admin');
            self::end();
        }
    }

    /**
     * @param int $userId
     * @param int $chatId
     * @return bool
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    public static function isChatAdmin(int $userId, int $chatId): bool
    {
        return in_array($userId, ApiController::getChatAdminsIds($chatId));
    }

    /**
     * @param int $userId
     * @param int $chatId
     * @return bool
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    public static function isChatMember(int $userId, int $chatId): bool
    {
        return in_array($userId, ApiController::getChatMembersIds($chatId));
    }

    /**
     * @param int $peer_id
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    public static function checkIsChat(int $peer_id): void
    {
        if (Utils::getTypeOfId($peer_id) !== 'chat') {
            Message::write(self::$_object['peer_id'], 'warning.chat_required');
            self::end();
        }
    }
}