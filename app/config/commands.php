<?php

use App\base\BaseCommand;

return [
    'hello' => [
        'class'   => \App\commands\HelloCommand::class,
        'aliases' => ['hi', 'привет'],
        'description' => <<<'D'
Команда hello.
По сути, это проверка работы бота.
Принимает неограниченное количество аргументов.
Пример: $ hello everybody
D
        ,
        'access' => BaseCommand::ACCESS_ANYONE,
    ],
    'kick' => [
        'aliases' => ['кик', 'кек', 'kik', 'пнх', 'ban', 'бан', 'drop'],
        'class'   => \App\commands\KickCommand::class,
        'description' => <<<'D'
Команда kick.
Исключает одного или нескольких участников из беседы.
Принимает неограниченное количество аргументов.
Пример: $ kick @username @username2
D
        ,
        'access' => BaseCommand::ACCESS_CHAT_ADMINS | BaseCommand::ACCESS_GROUP_ADMINS,
    ],
    'menu' => [
        'aliases' => ['help', 'меню', 'пасаны-помогите?'],
        'class'   => \App\commands\MenuCommand::class,
        'description' => <<<'D'
Команда menu.
Выводит в чат команды бота.
Не принимает аргументы.
Пример: $ menu
D
        ,
        'access' => BaseCommand::ACCESS_ANYONE,
    ],
    'rules' => [
        'aliases' => ['правила', 'что-можно'],
        'class'   => \App\commands\RulesCommand::class,
        'description' => <<<'D'
Команда rules.
Выводит в чат правила беседы.
Не принимает аргументы.
Пример: $ rules
D
        ,
        'access' => BaseCommand::ACCESS_CHAT_MEMBERS,
    ],
    'admins' => [
        'aliases' => ['админы', 'админ', 'admin'],
        'class'   => \App\commands\AdminsCommand::class,
        'description' => <<<'D'
Команда admins.
Выводит в чат богов-админов.
Не принимает аргументы.
Пример: $ admins
D
        ,
        'access' => BaseCommand::ACCESS_CHAT_MEMBERS,
    ],
    'wiki' => [
        'aliases' => ['вики'],
        'class'   => \App\commands\WikiCommand::class,
        'description' => <<<'D'
Команда wiki.
Возвращает информацию с сайта Wikipedia.
Принимает 1 аргумент: строка поиска.
Пример: $ wiki php
D
        ,
        'access' => BaseCommand::ACCESS_ANYONE,
    ],
    'translate' => [
        'aliases' => ['перевод', 'перевести'],
        'class'   => \App\commands\TranslateCommand::class,
        'description' => <<<'D'
Команда translate.
Переводит текст с помощью сервиса Yandex Translate.
Принимает 2 аргумента: флаг языка и текст.
Пример: $ translate -en Привет мир
D
        ,
        'access' => BaseCommand::ACCESS_ANYONE,
    ],
    'weather' => [
        'aliases' => ['погода', 'прогноз'],
        'class'   => \App\commands\WeatherCommand::class,
        'description' => <<<'D'
Команда weather.
Выводит прогноз погоды для заданного города.
Принимает 2 аргумента: лимит и город.
Аргумент 'лимит': '-w' -погода на 7 дней и '-d' (по умолчанию) -погода на сегодня.
Пример: $ weather -w Москва
D
        ,
        'access' => BaseCommand::ACCESS_ANYONE,
    ],
    'about' => [
        'aliases' => ['обо-мне', 'я', 'source', 'github'],
        'class'   => \App\commands\AboutCommand::class,
        'description' => <<<'D'
Выводит информацию о боте.
Аргументов не принимает.
Пример: $ about
D
        ,
        'access' => BaseCommand::ACCESS_ANYONE,
    ],
];
