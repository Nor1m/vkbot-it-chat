<?php

namespace App\commands;

use App\base\BaseCommand;
/**
 * Класс WeatherCommand
 * @package App\commands
 */
class WeatherCommand extends BaseCommand
{
    /**
     * @param array $argc
     *
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    public function run(array $argc): void
    {
        $limit = reset($argc);

        if (strpos($limit, "-") === 0) {
            array_shift($argc);
        } else {
            $limit = false;
        }

        $city = implode(" ", $argc);
        $content = $this->getWeather($city, $limit);
        $this->vk()->messages()->send(VK_TOKEN, array(
            'peer_id' => $this->object()['peer_id'],
            'message' => $content,
        ));
    }

    /**
     * @param $city
     * @param $limit
     * @return string
     */
    public function getWeather($city, $limit): string
    {
        if (!empty($city)) {

            $yql_query = 'select * from weather.forecast where woeid 
                in (select woeid from geo.places(1) where text="' . $city . '") and u="c"';

            $response = json_decode(file_get_contents(YAHOO_API_URL . http_build_query([
                'q' => $yql_query,
                'format' => 'json',
            ])));

            if (!empty($response->query->results->channel)) {

                $channel = $response->query->results->channel;
                $location = $channel->location->city . ", " . $channel->location->country;

                if ($limit == "-w") { // если на 10 дней

                    $text = 'Прогноз погоды на 10 дней в городе ' .
                        trim($city) . ' (' . $location . ')' . PHP_EOL . PHP_EOL;

                    foreach ($channel->item->forecast as $key => $day) {
                        $text .= "Дата: " . $this->geyFormatDate($day->date, $day->day) . PHP_EOL;
                        $text .= "Температура: от " . $day->low . " °C до " . $day->high . " °C" . PHP_EOL . PHP_EOL;
                    }

                    return $text . 'Источник: yahoo.com/news/weather';

                } else if ($limit == "-d" || !$limit) { // если на день

                    $wind_speed = $channel->wind->speed * 0.44704;
                    $temp = $channel->item->condition->temp;
                    $humidity = $channel->atmosphere->humidity;
                    $pressure = $channel->atmosphere->pressure;

                    $text  = 'Прогноз погоды на сегодня в городе ' .
                        trim($city) . ' (' . $location . ')' . PHP_EOL . PHP_EOL;
                    $text .= 'Температура: ' . $temp . ' °C' . PHP_EOL;
                    $text .= 'Скорость ветра: ' . round($wind_speed) . ' м/с' . PHP_EOL;
                    $text .= 'Влажность воздуха: ' . $humidity . '%' . PHP_EOL;
                    $text .= 'Давление: ' . round($pressure) . ' мм рт. ст.' . PHP_EOL . PHP_EOL;

                    return $text . 'Источник: yahoo.com/news/weather';

                } else {
                    return "Указан неверный параметр";
                }
            } else {
                return "Такого города не существует";
            }
        } else {
            return "Не указан город";
        }
    }

    /**
     * @param $date_str
     * @param $day_str
     * @return string
     */
    public function geyFormatDate($date_str, $day_str) {
        $date = new \DateTime($date_str);
        $date = $date->Format('d M');
        $date_str = str_replace(
            array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'),
            array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'),
            $date
        );
        $day = new \DateTime($day_str);
        $day = $day->Format('w');
        $days = array('Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье');
        return $date_str . " (" . $days[$day] . ")";
    }
}