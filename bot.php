<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once "bot/Bot.php";
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
