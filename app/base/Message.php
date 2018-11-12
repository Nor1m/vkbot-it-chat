<?php
/**
 * Created by PhpStorm.
 * User: ROMAN
 * Date: 09.11.2018
 * Time: 20:11
 */

namespace App\base;


use VK\Client\VKApiClient;

class Message
{
    /** @var VKApiClient */
    private static $_vk;

    const PREFIXES = array(
        'message',
        'error',
        'warning',
        'success',
    );

    /**
     * @param $vk
     */
    public static function init($vk): void
    {
        self::$_vk = $vk;
    }

    /**
     * @param int $peer_id
     * @param string $code
     * @param array $params
     *
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     * @throws \Exception
     */
    public static function write(int $peer_id, string $code, array $params = array()): void
    {
        $exploded = explode('.', $code);
        if (count($exploded) !== 2 || !in_array($exploded[0], self::PREFIXES)) {
            throw new \Exception("Неверный формат кода сообщения: \"$code\"");
        }

        $message = Config::message($exploded[0], $exploded[1]);

        self::$_vk->messages()->send(VK_TOKEN, array(
            'peer_id' => $peer_id,
            'message' => strtr($message, $params),
        ));
    }
}