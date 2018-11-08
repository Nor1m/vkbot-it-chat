<?php

namespace App;

class Bot
{
    /**
     * @var string Токен доступа сообщества
     */
    public $accessToken;

    /**
     * @var string Секретный ключ Callback API
     */
    public $secretKey;

    /**
     * @var string Строка для подтверждения
     * адреса сервера из настроек Callback API
     */
    public $confirmationKey;

    public function run(object $data)
    {
        /** @var object $data Получаем и декодируем уведомление */
        $data = json_decode(file_get_contents('php://input'));

        // Проверяем, что находится в поле "type"
        switch ($data->type) {
            // Если это уведомление для подтверждения адреса...
            case 'confirmation':
                // ...отправляем строку для подтверждения
                echo $this->confirmationKey;
                break;

            // Если это уведомление о новом сообщении...
            case 'message_new':
                // ...получаем id его автора
                $user_id = $data->object->from_id;
                // затем с помощью users.get получаем данные об авторе
                $user_info = json_decode(file_get_contents(
                    "https://api.vk.com/method/users.get?user_ids={$user_id}&access_token={$this->accessToken}&v=5.8.7"
                ));

                // и извлекаем из ответа его имя
                $user_name = $user_info->response[0]->first_name;

                // С помощью messages.send отправляем ответное сообщение
                $request_params = array(
                    'message'      => "Hello, {$user_name}!",
                    'user_id'      => $user_id,
                    'access_token' => $this->accessToken,
                    'v'            => '5.8.7',
                );

                $get_params = http_build_query($request_params);

                file_get_contents('https://api.vk.com/method/messages.send?' . $get_params);

                // Возвращаем "ok" серверу Callback API
                echo 'ok';

                break;
        }
    }
}
