<?php

namespace App\commands;

use App\base\BaseCommand;
use App\base\Deferred;
use App\base\Message;
use App\Log;

/**
 * Класс SpeechCommand
 * @package App\commands
 */
class SpeechCommand extends BaseCommand
{
    /**
     * @param array $argc
     *
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    public function run(array $argc): void
    {
        $object = $this->object();
        
        // если есть пересланное сообщение
        if (!empty($object['fwd_messages'])) {
            
            $fwd_message = $object['fwd_messages'][0];
            $link = $fwd_message->attachments[0]->audio_message->link_ogg;
            $duration = $fwd_message->attachments[0]->audio_message->duration;

            Message::write(
                $this->object()['peer_id'],
                "О, сейчас послушаю и тебе перескажу, жди"
            );

            $time = time();
            Log::dump($time);

            Deferred::add(function () use ($link, $duration, $time) {
                Log::dump(time());
                $content = $this->translate($link, $duration);
                Log::dump(time());
                Message::write(
                    $this->object()['peer_id'],
                    "Послушал, это заняло у меня целых "
                        . (time() - $time)
                        . ' секунд! Вот что там было:'
                        . PHP_EOL
                        . PHP_EOL
                        . $content
                );
            });
        }
    }
    
    /**
     * @param $link
     * @param $duration
     * @return string
     */
    public function translate($link, $duration): string
    {
        // если есть аудиозапись
        if (!$link) {
            return "Что-то не нахожу голосового в твоих сообщениях";
        }

        $max_duration = 30;
        if ($duration > $max_duration) {
            return "Голосовое сообщение слишком длинное. Макс {$max_duration} сек.";
        } else {
            $yandex_response = $this->speechKitYandex($link);
            $xml = simplexml_load_string($yandex_response);
            $data = $xml->variant[0][0];
            if (!empty($data)) {
                return $data;
            } else {
                return "Голосовое сообщение не распознано";
            }
        }
    }
    
    /**
     * @param $file
     * @return mixed
     */
    function speechKitYandex($file)
    {
        $topic = 'queries';
        $lang  = 'ru-RU';
        return $this->curl(
            'https://asr.yandex.net/asr_xml?' . http_build_query([
                'uuid'  => md5(time()),
                'key'   => YANDEXSPEECHKIT_API_KEY,
                'topic' => $topic,
                'lang'  => $lang,
            ]),
            file_get_contents($file),
            [
                'Content-Type: audio/ogg;codecs=opus',
                'Transfer-Encoding: chunked',
            ]
        );
    }    
    
    /**
     * @param $url
     * @param string $post
     * @param array $headers
     * @return mixed
     */
    function curl($url, $post = '', $headers = array())
    {
        if ($curl = curl_init()) {
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            $out = curl_exec($curl);
            curl_close($curl);
            return $out;
        }

        throw new \RuntimeException("Ошибка, не удалось запустить curl");
    }
        
}
