<?php

namespace App\commands;

use App\base\BaseCommand;

/**
 * Класс KickCommand
 * @package App\commands
 */
class KickCommand extends BaseCommand
{
    /**
     * @param $object array
     * @param $user array
     *
     * @param array $argc
     *
     * @throws \VK\Exceptions\VKApiException
     * @throws \VK\Exceptions\VKClientException
     */
    public function run(array $object, array $user, array $argc): void
    {
    	if (!$argc || !$object) return;

    	$chat_id = $object['peer_id'] - 2000000000;
    	$flag_gif = true;

    	foreach ($argc as $key => $value) {
    		if ( preg_match('~\[id(.\d+)\|~', $value, $matches, PREG_OFFSET_CAPTURE) ) {

    			// если несколько юзеров то гифку показываем только раз
    			if ( $flag_gif ) {
	    			// отправляем юзеру гифку
			        $this->vk()->messages()->send(VK_TOKEN, array(
			        	'chat_id' => $chat_id,
			            'peer_id' => $object['peer_id'],
			            'attachment' => VK_DOC_BEFORE_KICK,
			        ));
			    }

        		// исключаем юзера из беседы
    			$this->vk()->messages()->removeChatUser(VK_TOKEN, array(
		            'chat_id' => $chat_id,
		            'user_id' => $matches[1][0],
		            'v' => "5.87"
		        ));

		        $flag_gif = false;
    		}
    	}
    }
}