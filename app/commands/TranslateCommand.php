<?php

namespace App\commands;

use App\base\BaseCommand;
/**
 * Класс TranslateCommand
 * @package App\commands
 */
class TranslateCommand extends BaseCommand
{
    /**
     * @param array $argc
     *
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    public function run(array $argc): void
    {
        $query = implode(" ", $argc);
        $content = $this->translate($query);
        $this->vk()->messages()->send(VK_TOKEN, array(
            'peer_id' => $this->object()['peer_id'],
            'message' => $content,
        ));
    }

    /**
     * @param $text
     * @return string
     */
    public function translate($text): string
    {
        $response = file_get_contents(YANDEXTRANSLATE_API_URL . http_build_query([
            'key' => YANDEXTRANSLATE_API_KEY,
            'text' => $text,
            'lang' => 'ru',
        ]));

        if (!empty(json_decode($response)->text[0])) {
            return json_decode($response)->text[0] .
                "\n\nПереведено сервисом «Яндекс.Переводчик» http://translate.yandex.ru/";
        } else {
            return "Не могу это перевести :(";
        }
    }
}