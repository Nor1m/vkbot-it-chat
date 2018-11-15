<?php

namespace App\commands;

use App\base\BaseCommand;
/**
 * Класс WikiCommand
 * @package App\commands
 */
class WikiCommand extends BaseCommand
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
        $content = $this->getWiki($query);
        $this->vk()->messages()->send(VK_TOKEN, array(
            'peer_id' => $this->object()['peer_id'],
            'message' => $content,
        ));
    }

    /**
     * @param $search
     * @return string
     */
    public function getWiki($search): string
    {
        $response = file_get_contents(WIKI_API_URL . http_build_query([
            'action' => 'opensearch',
            'prop' => 'info',
            'format' => 'json',
            'search' => $search,
            'inprop' => 'url',
        ]));

        if (!empty(json_decode($response)[2][0])) {
            return json_decode($response)[2][0] .
                "\n\nИсточник: " . json_decode($response)[3][0];
        } else {
            return "Ничего не найдено :(";
        }
    }
}