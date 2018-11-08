<?php

use App\Bot;
use App\Log;

if (!isset($_REQUEST)) {
    return;
}

require_once "app/config/config.php";
require_once "vendor/autoload.php";

$log = new Log();

$log->write("Загрузка проекта");

$bot                  = new Bot();
$bot->accessToken     = VK_TOKEN;
$bot->secretKey       = VK_SECRET_KEY;
$bot->confirmationKey = VK_CONFIRMATION_TOKEN;
$bot->run(json_decode(
    file_get_contents('php://input'),
    false
));