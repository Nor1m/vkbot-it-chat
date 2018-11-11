<?php

use App\base\BaseCommand;

return [
    'hello' => [
        'description' => 'Команда привет. По сути, это проверка работы бота.'
            . ' Принимает неограниченное количество аргументов'
            . ' Пример: $ hello everybody',
        'access' => BaseCommand::ACCESS_ANYONE,
    ],
    'kick' => [
        'description' => 'Команда kick. Исключает одного или нескольких участников из беседы.'
            . ' Принимает неограниченное количество аргументов'
            . ' Пример: $ kick @username @username2',
        'access' => BaseCommand::ACCESS_CHAT_ADMINS | BaseCommand::ACCESS_GROUP_ADMINS,
    ],
    'menu' => [
        'description' => 'Команда menu. Выводит в чат команды бота.'
            . ' Не принимает аргументы'
            . ' Пример: $ menu',
        'access' => BaseCommand::ACCESS_ANYONE,
    ],
    'rules' => [
        'description' => 'Команда rules. Выводит в чат правила беседы.'
            . ' Не принимает аргументы'
            . ' Пример: $ rules',
        'access' => BaseCommand::ACCESS_ANYONE,
    ],
];