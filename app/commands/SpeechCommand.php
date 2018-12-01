<?php

namespace App\commands;

use App\base\BaseCommand;
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
            $content = $this->translate($link, $duration);
            
        } else { 
            die("ok");
        }
        
        $this->vk()->messages()->send(VK_TOKEN, array(
            'peer_id' => $this->object()['peer_id'],
            'message' => $content,
        ));
    }
    
    /**
     * @param $link
     * @param $duration
     * @return string
     */
    public function translate($link, $duration): string
    {
        // если есть аудиозапись
        if ($link) {
            $max_duration = 30;
            if ($duration > $max_duration) {
                return "Голосовое сообщение слишком длинное. Макс {$max_duration} сек.";
            } else {
                $yandex_response = $this->speechkitYandex($link);
                $xml = simplexml_load_string($yandex_response);
                $data = $xml->variant[0][0];
                if (!empty($data)) {
                    return $data;
                } else {
                    return "Голосовое сообщение не распознано";
                }
            }
        }
        else {
            die("ok");
        }
    }
    
    /**
     * @param $file
     * @return mixed
     */
    function speechkitYandex($file)
    {
        $topic = 'queries';
        $lang = 'ru-RU';
        return $this->curl(
            'https://asr.yandex.net/asr_xml?uuid=' . md5(time()) . '&key=' . YANDEXSPEECHKIT_API_KEY . '&topic=' . $topic . '&lang=' . $lang,
            file_get_contents($file), array(
                'Content-Type: audio/ogg;codecs=opus',
                'Transfer-Encoding: chunked'
            )
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
    }
        
}
