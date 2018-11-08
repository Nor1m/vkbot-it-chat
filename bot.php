<?php

use App\Log;
use App\ServerHandler;
use VK\Client\VKApiClient;

if (!isset($_REQUEST)) {
    return;
}

require_once "app/config/config.php";
require_once "vendor/autoload.php";

Log::init(ROOT_PATH . 'storage/logs/log.txt');

Log::write("Загрузка проекта");

$handler = new ServerHandler(new VKApiClient());

$data = json_decode(file_get_contents('php://input'));

$handler->parse($data);

Log::close();