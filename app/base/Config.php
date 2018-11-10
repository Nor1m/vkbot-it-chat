<?php

namespace App\base;


class Config
{
    private static $_configFolder;

    private static $_attachments;

    private static $_messages;

    private static $_commands;

    public static function init($configFolder) {
        self::$_configFolder = $configFolder;
    }

    /**
     * @param string $code
     * @return string
     * @throws \Exception
     */
    public static function attachment(string $code): string
    {
        if (!self::$_attachments) {
            self::$_attachments = require_once self::$_configFolder . '/attachments.php';
        }

        if (isset(self::$_attachments[$code])) {
            return self::$_attachments[$code];
        }

        throw new \Exception("Неизвестный код вложения: $code");
    }

    /**
     * @param string $prefix
     * @param string $code
     * @return string
     * @throws \Exception
     */
    public static function message(string $prefix, string $code): string
    {
        if (!self::$_messages) {
            self::$_messages = require_once self::$_configFolder . '/messages.php';
        }

        if (isset(self::$_messages[$prefix][$code])) {
            return self::$_messages[$prefix][$code];
        }

        throw new \Exception("Неизвестный код сообщения: $prefix.$code'");
    }

    public static function commands(): array
    {
        if (!self::$_commands) {
            self::$_commands = require_once self::$_configFolder . '/commands.php';
        }

        return self::$_commands;
    }
}