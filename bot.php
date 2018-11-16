<?php

use App\base\Config;
use App\base\Message;
use App\base\ApiController;
use App\Log;
use App\ServerHandler;
use VK\Client\VKApiClient;

if (!isset($_REQUEST)) {
    return;
}

require_once "app/config/env.php";

if (!BOT_STATUS) die("ok"); // отрубаем всё

require_once "vendor/autoload.php";

Log::init(ROOT_PATH . 'storage/logs/log.txt');

set_error_handler(function ($errno, $errstr, $errfile, $errline, array $errcontext) {
    // error was suppressed with the @-operator
    if (0 === error_reporting()) {
        Log::write("Ошибка погашена с помощью @: $errstr, $errfile ($errline)");
        return false;
    }
 
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

Log::write("Загрузка проекта");

$vk = new VKApiClient('5.81');

ApiController::init($vk);
Message::init($vk);
Config::init(ROOT_PATH . 'app/config');
\App\base\Db::init(DB, DB_HOST, DB_NAME, DB_USER, DB_PASSWORD, array(
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false
));

$handler = new ServerHandler($vk);

$data = json_decode(file_get_contents('php://input'));

$handler->parse($data);

Log::close();