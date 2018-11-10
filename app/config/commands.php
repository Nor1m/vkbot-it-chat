<?php

use App\base\BaseCommand;

return [
    'hello' => [
        'description' => 'Команда привет. По сути, это проверка работы бота.'
            . ' Принимает неограниченное количество аргумантов',
        'access' => BaseCommand::ACCESS_ANYONE,
    ],
    'kick' => [
        'description' => 'Команда привет. По сути, это проверка работы бота.'
            . ' Принимает неограниченное количество аргумантов',
        'access' => BaseCommand::ACCESS_CHAT_ADMINS | BaseCommand::ACCESS_GROUP_ADMINS,
    ],
    'menu' => [
        'description' => 'Команда привет. По сути, это проверка работы бота.'
            . ' Принимает неограниченное количество аргумантов',
        'access' => BaseCommand::ACCESS_ANYONE,
    ],
    'rules' => [
        'description' => 'Команда привет. По сути, это проверка работы бота.'
            . ' Принимает неограниченное количество аргумантов',
        'access' => BaseCommand::ACCESS_ANYONE,
    ],
];