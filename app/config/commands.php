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
Принимает неограниченное количество аргументов.
Пример: $ wiki hello world
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
    'user' => [
        'class' => \App\commands\UserCommand::class,
        'description' => <<<'D'
Команда user.
- выводит информацию о пользователе, который её вызвал;
- изменяет сохранённую информацию о пользователе.

Примеры использования:

$ user name <имя> - изменение имени

$ user surname <фамилия> - изменение фамилии

$ user patr <отчество> - изменение отчества

$ user tech [-add] <код> - добавление технологии по коду в свой стэк технологий.
Если попытаетесь добавить технологию, которой ещё нет в нашей БД,
это предложение поставится на рассмотрение, и позже,
после утверждения, технология будет добавлена

$ user tech (-rm|-del) {<код>|<номер>} - удаление технологий по коду или
номеру из своего стэка технологий

$ user tech (-mov|-move|-mv) (<код>|<номер>) (<номер>|up|down|end) - изменение порядкового
номера технологии в стеке

$ user tech -sort - сортировка технологий в стеке по алфавиту
D
        ,
        'access' => BaseCommand::ACCESS_ANYONE,
    ],
    'tech' => [
        'class' => \App\commands\TechCommand::class,
        'description' => <<<'D'
Команда tech. Выводит доступные технологии, технологии на утверждении,
утверждённые и отклонённые технологии

Примеры использования:
$ tech [list] - список доступных технологий

$ tech info <код> - Информация о технологии

$ tech edit <код> (-name|-nam) <имя> - Изменение имени технологии

$ tech edit <код> (-cod|-code) <имя> - Изменение кода технологии

$ tech edit <код> (-desc|-description) <имя> - Изменение описания технологии

$ tech (proposal|proposed) <стр> - постраничный вывод предложенных технологий

$ tech (proposal|proposed) -apply <код> - утвердить технологию

$ tech (proposal|proposed) -deny <код> - отклонить технологию

$ tech (proposal|proposed) (-rm|-del) <код> - удалить предложение
технологии (безвозвратно, не рекомендуется)

$ tech (proposal|proposed) -applied <стр> - постраничный вывод утверждённых технологий

$ tech (proposal|proposed) -denied <стр> - постраничный вывод отклонённых технологий

$ tech (proposal|proposed) -closed <стр> - постраничный вывод закрытых предложений
D
        ,
        'access' => BaseCommand::ACCESS_ANYONE,
    ]
];