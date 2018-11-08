<?php

namespace App;

class Log
{
    protected $file;

    public function __construct()
    {
        $this->file = fopen(ROOT_PATH . 'storage/logs/log.txt', 'a+');
        if ($this->file == false)
            throw new \Exception('Не удается открыть файл ' . $fileName . "\n");
    }

    public function write($string)
    {
        fwrite($this->file, date("d.m.Y/H:i:s") . ": " . $string);
    }

    public function __destruct()
    {
        fclose($this->file);
    }
}