<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

/**
 * @var string Документ для отправки юзеру перед исключением из беседы
 */
const VK_DOC_BEFORE_KICK = "";

/**
 * @var string ID группы ВК
 */
const VK_GROUP_ID = 173634918;

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
 * @var string[] Список доступных команд для бота
 */
const AVAILABLE_CMDS = array();

/**
 * @var string Правила беседы вк
 */
const VK_RULES = "
	Правила беседы: 
	- Не спамить ! 
	- Не флудить ! 
	- Не рекламировать ничего ! 
	- Писать без ошибок ! 
	- Не оскорблять никого !";

