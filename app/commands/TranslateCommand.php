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
        $lang = reset($argc);
        if (strpos($lang, "-") === 0) {
            $lang = mb_substr($lang, 1);
            array_shift($argc);
        } else {
            $lang = "ru";
        }
        $query = implode(" ", $argc);
        $content = $this->translate($query, $lang);
        $this->vk()->messages()->send(VK_TOKEN, array(
            'peer_id' => $this->object()['peer_id'],
            'message' => $content,
        ));
    }


    /**
     * @param string $text
     * @param string $lang
     * @return string
     */
    public function translate($text, $lang = "ru"): string
    {
        // если код языка такой есть
        if (in_array($lang, YANDEXTRANSLATE_API_LANG_ARRAY)) {
            $response_translate = file_get_contents(YANDEXTRANSLATE_API_URL . "translate?" . http_build_query([
                'key' => YANDEXTRANSLATE_API_KEY,
                'text' => $text,
                'lang' => $lang,
            ]));
            if (!empty(json_decode($response_translate)->text[0])) {
                return json_decode($response_translate)->text[0] .
                    "\n\nПереведено сервисом «Яндекс.Переводчик» http://translate.yandex.ru/";
            } else {
                return "Не могу это перевести :(";
            }
        } else {
            return "Неверный код языка";
        }
    }
}