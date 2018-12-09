<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


// Конфигурация бота

/**
 * @var string Дебаг. Если включён, будут работать логи и прочие плюхи
 */
const APP_DEBUG = true;

/**
 * @var bool статус бота
 */
const BOT_STATUS = true;

/**
 * @var array информация о боте
 */
const BOT_INFO = array(
    'name' => 'vkbot it chat',
    'description' => 'Чат бот для беседы вк',
    'version' => '1.0',
    'link' => 'https://github.com/Nor1m/vkbot-it-chat',
);

const BOT_ANCHOR = '!';

// Конфигурация VK

/**
 * @var string ID группы ВК
 */
const VK_GROUP_ID = 167154401;

/**
 * @var string Токен доступа сообщества
 */
const VK_TOKEN = "";
/**
 * @var string Секретный ключ Callback API
 */
const VK_SECRET_KEY = "";

/**
 * @var string Строка для подтверждения
 * адреса сервера из настроек Callback API
 */
const VK_CONFIRMATION_TOKEN = "";

/**
 * @var string Путь к корню проекта
 */
defined("ROOT_PATH")
or define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT'] . "/vkapi2/" );

// Конфигурация бд

const DB = 'mysql';
const DB_HOST = 'localhost';
const DB_NAME = '';
const DB_USER = '';
const DB_PASSWORD = '';

// Конфигурация API Wikipedia

/**
 * @var string URL API Wikipedia
 */
const WIKI_API_URL = "https://ru.wikipedia.org/w/api.php?";

// Конфигурация API YANDEX TRANSLATE

/**
 * @var string URL API YANDEX TRANSLATE
 */
const YANDEXTRANSLATE_API_URL = "https://translate.yandex.net/api/v1.5/tr.json/";

/**
 * @var string API KEY YANDEX TRANSLATE
 */
const YANDEXTRANSLATE_API_KEY = "";

/**
 * @var string массив языков API YANDEX TRANSLATE
 */
const YANDEXTRANSLATE_API_LANG_ARRAY = array('ru', 'az', 'be', 'bg', 'ca', 'cs', 'da', 'de', 'el', 'en', 'es', 'et', 'fi', 'fr', 'hr', 'hu', 'hy', 'it', 'lt', 'lv', 'mk', 'nl', 'no', 'pl', 'pt', 'ro', 'sk', 'sl', 'sq', 'sr', 'sv', 'tr', 'uk');

/**
 * @var string API YANDEX SPEECHKIT
 */
const YANDEXSPEECHKIT_API_KEY = "";

// Конфигурация YAHOO API

/**
 * @var string URL YAHOO API
 */
const YAHOO_API_URL = "http://query.yahooapis.com/v1/public/yql?";


