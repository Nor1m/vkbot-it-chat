<?php return [
    'message' => [
        'leave' => "Люди нас покидают, Милорд",

        'greeting' => <<<GREETING
{name} {surname}, добро пожаловать в беседу!
Правила беседы - #rules
Чат в тг - https://t.me/it_default
Группа в вк - https://vk.com/defbot
GREETING
        ,

        'menu' => "Доступные команды:{commands}",
        'rules' => <<<RULES
Правила беседы:
- Не спамить!
- Не флудить!
- Не рекламировать ничего!
- Писать без ошибок!
- Не оскорблять никого!
RULES
        ,
        'admins' => "Великие админы нашей беседы:{admins}",
        'user_info' => <<<INFO
Вот что я знаю о тебе:
Фамилия: {surname}
Имя: {name}
INFO
        ,
        'user_info_patr'  => 'Отчество: {patr}' . PHP_EOL,
        'user_stack_head' => 'Стак:' . PHP_EOL,
        'user_stack_add' => 'Добавление технологий в стак.',
        'user_stack_added' => "{tech} - технология добавлена",
        'tech_item' => '{ord}. {name}',
        'tech_item_with_code' => '{ord}. {name} ({code})',
        'tech_info' => <<<INFO
Технология: {name}
Код в моей базе: {code}
Описание: {description}
INFO
        ,
        'proposed_added' => 'Я её запомню. Если позже её утвердят, она у тебя появится',
        'proposed_not_added' => 'Добавлять я её не собираюсь',
        'proposed_list' => 'Предложенные технологии.',
        'proposed_list_closed' => 'Предложенные технологии.',
        'proposed_list_applied' => 'Предложенные технологии.',
        'proposed_list_denied' => 'Предложенные технологии.',
    ],

    'error' => [
        'smf_wrong' => 'Что-то явно пошло не так, не трогайте меня',
    ],

    'warning' => [
        'wrong_cmd' => 'Такой команды не существует. Чтобы посмотреть список команд введите "$ menu"',
        'chat_required' => 'Это действие можно выполнить только находясь в беседе',
        'not_admin' => 'У Вас нет доступа к этой команде',
        'not_kick_admin' => 'Вы не можете исключить админа беседы',
        'user_not_in_chat' => 'Такого пользователя нет в беседе',
        'no_args' => 'Неверное число аргументов',
        'no_tech' => '{tech} - такой технологии нет.',
        'user_stack_no_tech' => 'Такой технологии у тебя в стаке нет',
    ],

    'success' => [
        'done' => 'Сделано',
    ],
];