<?php

use App\base\BaseCommand;

return [
    'hello' => [
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
        'description' => <<<'D'
Команда rules.
Выводит в чат правила беседы.
Не принимает аргументы.
Пример: $ rules
D
        ,
        'access' => BaseCommand::ACCESS_ANYONE,
    ],
    'admins' => [
        'description' => <<<'D'
Команда admins.
Выводит в чат богов-админов.
Не принимает аргументы.
Пример: $ admins
D
        ,
        'access' => BaseCommand::ACCESS_CHAT_MEMBERS,
    ],
];