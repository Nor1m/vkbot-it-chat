<?php
		
	require "config.php";

	if (!isset($_REQUEST)) {
	    return;
	}
	
	//Строка для подтверждения адреса сервера из настроек Callback API
	$confirmation_token = VK_CONFIRMATION_TOKEN;

	//Ключ доступа сообщества
	$token = VK_TOKEN;

	// Secret key
	$secretKey = VK_SECRET_KEY;

	//Получаем и декодируем уведомление 
	$data = json_decode(file_get_contents('php://input')); 

	//Проверяем, что находится в поле "type" 
	switch ($data->type) { 
		//Если это уведомление для подтверждения адреса... 
		case 'confirmation': 
			//...отправляем строку для подтверждения 
			echo $confirmation_token; 
		break; 

		//Если это уведомление о новом сообщении... 
		case 'message_new': 
			//...получаем id его автора 
			$user_id = $data->object->from_id; 
			//затем с помощью users.get получаем данные об авторе 
			$user_info = json_decode(file_get_contents("https://api.vk.com/method/users.get?user_ids={$user_id}&access_token={$token}&v=5.8.7")); 

			//и извлекаем из ответа его имя 
			$user_name = $user_info->response[0]->first_name; 

			//С помощью messages.send отправляем ответное сообщение 
			$request_params = array( 
				'message' => "Hello, {$user_name}!", 
				'user_id' => $user_id, 
				'access_token' => $token, 
				'v' => '5.8.7' 
			); 

			$get_params = http_build_query($request_params); 

			file_get_contents('https://api.vk.com/method/messages.send?'. $get_params); 

			//Возвращаем "ok" серверу Callback API 

			echo('ok'); 

		break; 

	} 
?> 