<?php

namespace App\base;


use App\Log;
use VK\Client\VKApiClient;

class ApiController
{
    /** @var VKApiClient */
    private static $_vk;

    /** @var array[]  */
    private static $_chatMembers = [];

    /** @var array[]  */
    private static $_chatAdmins = [];

    /** @var int[][] */
    private static $_chatAdminsIds = [];

    /** @var int[][] */
    private static $_chatMembersIds = [];

    /**
     * @param $vk
     */
    public static function init($vk): void
    {
        self::$_vk = $vk;
    }

    /**
     * Метод загружает из вк
     *
     * @param int $chatId
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    private static function loadChatMembers(int $chatId): void
    {
        $members = self::$_vk->messages()->getConversationMembers(
            VK_TOKEN,
            array('peer_id' => $chatId)
        );

        $admin_items = array_filter($members['items'], function ($item) {
            return !empty($item['is_admin']);
        });

        $admins_ids = array_map(
            function ($item) {
                return $item['member_id'];
            },
            $admin_items
        );

        $count = 0;
        $admins = [
            'items' => $admin_items,
        ];

        if (!empty($members['profiles'])) {
            foreach ($members['profiles'] as $group) {
                if (in_array($group['id'], $admins_ids)) {
                    $count++;
                    $admins['profiles'][] = $group;
                }
            }
        }

        if (!empty($members['groups'])) {
            foreach ($members['groups'] as $group) {
                if (in_array(-$group['id'], $admins_ids)) {
                    $count++;
                    $admins['groups'][] = $group;
                }
            }
        }

        $admins['count'] = $count;

        $members_ids = array_map(
            function ($item) {
                return $item['member_id'];
            },
            $members['items']
        );

        self::$_chatMembers[$chatId] = $members;
        self::$_chatMembersIds[$chatId] = $members_ids;
        self::$_chatAdmins[$chatId] = $admins;
        self::$_chatAdminsIds[$chatId] = $admins_ids;

        if (APP_DEBUG) {
            Log::write(
                "Loaded chat members for chat $chatId: "
                . json_encode(self::$_chatMembers[$chatId])
            );

            Log::write(
                "Saved chat members ids for chat $chatId: "
                . json_encode(self::$_chatMembersIds[$chatId])
            );

            Log::write(
                "Saved chat admins for chat $chatId: "
                . json_encode(self::$_chatAdmins[$chatId])
            );

            Log::write(
                "Saved chat admins ids for chat $chatId: "
                . json_encode(self::$_chatAdminsIds[$chatId])
            );
        }
    }

    /**
     * @param int $chatId
     *
     * @return array
     *
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     * @throws \Exception
     */
    public static function getChatMembers(int $chatId): array
    {
        if (empty(self::$_chatMembers[$chatId])) {
            self::loadChatMembers($chatId);
        }

        return self::$_chatMembers[$chatId];
    }

    /**
     * @param int $chatId
     *
     * @return array
     *
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     * @throws \Exception
     */
    public static function getChatAdmins(int $chatId): array
    {
        if (empty(self::$_chatAdmins[$chatId])) {
            self::loadChatMembers($chatId);
        }

        return self::$_chatAdmins[$chatId];
    }

    /**
     * @param int $chatId
     * @return array
     *
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    public static function getChatAdminsIds(int $chatId): array
    {
        if (empty(self::$_chatAdminsIds[$chatId])) {
            self::loadChatMembers($chatId);
        }

        return self::$_chatAdminsIds[$chatId];
    }

    /**
     * @param int $chatId
     * @return int[]
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    public static function getChatMembersIds(int $chatId)
    {
        if (empty(self::$_chatMembersIds[$chatId])) {
            self::loadChatMembers($chatId);
        }

        return self::$_chatMembersIds[$chatId];
    }
}
