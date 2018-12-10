<?php

namespace App;

/**
 * Метод доступа к экземпляру приложения
 * @return \App\Application
 */
function app(): Application
{
    static $app = null;
    if ($app === null) {
        $app = loadApp();
    }

    return $app;
}

/**
 * @return Application
 */
function loadApp(): Application
{
    $app = new Application(
        VK_TOKEN,
        VK_SECRET_KEY,
        VK_CONFIRMATION_TOKEN,
        VK_GROUP_ID
    );

    return $app;
}