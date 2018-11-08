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
    public static function init($log_path)
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

    public static function write($string)
    {
        fwrite(self::$file, date("d.m.Y/H:i:s") . ": " . $string . PHP_EOL);
    }

    public static function close()
    {
        fclose(self::$file);
    }
}