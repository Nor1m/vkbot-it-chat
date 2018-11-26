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
            $limit = '-d';
        }

        $city    = implode(" ", $argc);
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
        if (empty($city)) {
            return "Не указан город";
        }

        $yql_query = 'select * from weather.forecast where woeid '
            . 'in (select woeid from geo.places(1) where text="'
            . $city
            . '") and u="c"';

        $response = json_decode(file_get_contents(
            YAHOO_API_URL . http_build_query([
                'q'      => $yql_query,
                'format' => 'json',
            ])
        ));

        if (empty($response->query->results->channel)) {
            return "Такого города не существует";
        }

        $channel  = $response->query->results->channel;
        $location = $channel->location->city . ", " . $channel->location->country;

        if ($limit == "-w") { // если на 7 дней

            $text = 'Прогноз погоды на 7 дней в городе ' .
                trim($city) . ' (' . $location . ')' . PHP_EOL . PHP_EOL;

            foreach ($channel->item->forecast as $key => $day) {
                if ($key >= 7) {
                    break;
                }
                $text .= "Дата: " . $this->getFormatDate($day->date, $day->day) . PHP_EOL;
                $text .= "Температура: от " . $day->low . " °C до " . $day->high . " °C" . PHP_EOL . PHP_EOL;
            }

            return $text . 'Источник: yahoo.com/news/weather';

        } else {
            if ($limit != "-d") {
                return "Указан неверный параметр";
            }

            $wind_speed = $channel->wind->speed * 0.44704;
            $temp       = $channel->item->condition->temp;
            $humidity   = $channel->atmosphere->humidity;
            $pressure   = $channel->atmosphere->pressure;

            $text = 'Прогноз погоды на сегодня в городе ' .
                trim($city) . ' (' . $location . ')' . PHP_EOL . PHP_EOL;
            $text .= 'Температура: ' . $temp . ' °C' . PHP_EOL;
            $text .= 'Скорость ветра: ' . round($wind_speed) . ' м/с' . PHP_EOL;
            $text .= 'Влажность воздуха: ' . $humidity . '%' . PHP_EOL;
            $text .= 'Давление: ' . round($pressure) . ' мм рт. ст.' . PHP_EOL . PHP_EOL;

            return $text . 'Источник: yahoo.com/news/weather';
        }
    }

    /**
     * @param $date_str
     * @param $day_str
     * @return string
     */
    public function getFormatDate($date_str, $day_str): string
    {
        setlocale(LC_ALL, 'ru_RU.UTF-8');
        return strftime("%e %b (%a)", strtotime($date_str . $day_str));
    }
}
