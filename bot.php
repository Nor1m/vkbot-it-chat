<?php

use VkBot\Bot;

if (!isset($_REQUEST)) {
    return;
}

require_once "config.php";

$bot                  = new Bot();
$bot->accessToken     = VK_TOKEN;
$bot->secretKey       = VK_SECRET_KEY;
$bot->confirmationKey = VK_CONFIRMATION_TOKEN;
$bot->run(json_decode(
    file_get_contents('php://input'),
    false
));
