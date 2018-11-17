<?php

namespace App;

/**
 * Класс Log
 * @package App
 */
class Log
{
    /**
     * @var resource
     */
    protected static $file;

    /**
     * @param $log_path
     * @throws \Exception
     */
    public static function init(string $log_path): void
    {
        self::$file = fopen($log_path, 'a+');
        if (self::$file == false) {
            throw new \Exception('Не удается открыть файл ' . $log_path . PHP_EOL);
        }
    }

    private function __construct()
    {
        throw new \LogicException("Log can not have instances!!");
    }

    public static function record(string $string): void
    {
        fwrite(self::$file, date("[d.m.Y H:i:s]") . " " . $string . PHP_EOL);
    }

    public static function write(string $string): void
    {
        self::record('[info] ' . $string);
    }

    public static function dump($var): void
    {
        // не выводить во время дебага
        if (!APP_DEBUG) {
            return;
        }

        ob_start();
        var_dump($var);
        self::record('[dump] ' . ob_get_clean());
    }

    public static function error(string $string): void
    {
        self::record('[error] ' . $string);
    }

    public static function warning(string $string): void
    {
        self::record('[warning] ' . $string);
    }

    public static function close(): void
    {
        fclose(self::$file);
    }
}