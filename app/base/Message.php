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
     * @param string $str
     *
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    public static function write(int $peer_id, string $str): void
    {
        self::$_vk->messages()->send(VK_TOKEN, array(
            'peer_id' => $peer_id,
            'message' => $str,
        ));
    }

    /**
     * @param $code
     * @param array $params
     * @return string
     * @throws \Exception
     */
    public static function t(string $code, array $params = array()): string
    {
        $exploded = explode('.', $code);
        if (count($exploded) !== 2 || !in_array($exploded[0], self::PREFIXES)) {
            throw new \Exception("Неверный формат кода сообщения: \"$code\"");
        }

        return strtr(Config::message($exploded[0], $exploded[1]), $params);
    }
}