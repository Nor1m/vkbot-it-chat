<?php

namespace App;

use App\Traits\PropertyAccess;
use VK\CallbackApi\Server\VKCallbackApiServerHandler;

class Application extends VKCallbackApiServerHandler
{
    use PropertyAccess;

    public function __construct($vkToken, $vkSecretKey, $vkGroupId, $vkConfirmation)
    {

    }

    private $db;
    private $protect;
    private $config;
    private $handler;

    public function run(object $data)
    {

    }

    /**
     * @param int $group_id
     * @param null|string $secret
     */
    function confirmation(int $group_id, ?string $secret)
    {

    }
}