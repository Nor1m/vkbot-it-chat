<?php

namespace App\base;


use VK\Client\VKApiClient;

/**
 * Класс BaseCommand
 * @package App\base
 */
abstract class BaseCommand
{
    private $_vk;

    public function __construct($vk)
    {
        $this->_vk = $vk;
    }

    protected function vk(): VKApiClient
    {
        return $this->_vk;
    }

    public abstract function run(array $object, array $user, array $argc): void;
}