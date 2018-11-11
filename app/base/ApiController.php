<?php

namespace App\base;


use VK\Client\VKApiClient;

class ApiController
{
    /** @var VKApiClient */
    private static $_vk;

    /**
     * @param $vk
     */
    public static function init($vk): void
    {
        self::$_vk = $vk;
    }

    /**
     * @param int $peer_id
     *
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     * @throws \Exception
     */
    public static function getChatMembers(int $peer_id): array
    {
        return self::$_vk->messages()->getConversationMembers(VK_TOKEN, array(
            'peer_id' => $peer_id,
        ));
    }

    /**
     * @param int $membersArray
     *
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     * @throws \Exception
     */
    public static function getChatAdmins(int $peer_id): array
    {
        $adminsArray = array();
        $membersArray = self::getChatMembers($peer_id);
        foreach ($membersArray['items'] as $key => $value) {
            if (!empty($value['is_admin'])) {
                if ($value['is_admin']) {
                    $adminsArray[] = $value['member_id'];
                }
            }
        }
        return $adminsArray;
    }
}