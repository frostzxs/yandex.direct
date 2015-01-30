<?php




/**
 * ReadYandexDirectClass.php
 * @author Шакиров
 * @version 1.0
 * @created 30-янв-2015 17:14:49
 */
class ReadYandexDirectClass
{

	/**
	 * Данные запроса - ассоциативный массив. Ключи - названия кампании. Значения -
	 * ассоциативные массивы,  где ключи - параметры кампании, значения - значения
	 * параметров, либо ключи - объявления, значения - ассоциативные массивы,  где
	 * ключи - параметры объявления, значения - значения параметров, либо ключи -
	 * ключевые слова, значения - ассоциативные массивы,  где ключи - параметры
	 * ключевых слов, значения - значения параметров
	 */
	public $campaigns;
	private $requestData;

	public function requestCampaigns()
	{

		//{864FE460-5D48-4a55-8ED6-9CFC7C36135F}


		//<?
		//1. Формируем список кампаний и их данных. (запрос методом GetCampaignsList)  
		//2. Затем формируем список объявлений кампаний и список ключевых слов кампаний. (запрос методом GetBanners)
		 
		# PHP 4 >= 4.3.0, PHP 5
	
	
		# метод API 
		$method = 'GetCampaignsList';
	
	
		# входные данные 
		$params = array('FROSTZXS');
		 
		# перекодировка строковых данных в UTF-8
		function utf8($struct) {
		    foreach ($struct as $key => $value) {
		        if (is_array($value)) {
		            $struct[$key] = utf8($value);
		        }
		        elseif (is_string($value)) {
		            $struct[$key] = utf8_encode($value);
		        }
		    }
		    return $struct;
		}
	
		# формирование запроса    
		$request = array(
		    'token'=> $this->requestData["access_token"], 
		    'method'=> $method,
		    'param'=> utf8($params),
		    'locale'=> 'ru',
		);
	
		 
		# преобразование в JSON-формат
		$request = json_encode($request);
	
		 
		# параметры запроса
		$opts = array(
		    'http'=>array(
		        'method'=>"POST",
		        'content'=>$request,
		    )
		); 
		 
		 
		# создание контекста потока
		$context = stream_context_create($opts);
	
		# отправляем запрос и получаем ответ от сервера
		if ($_GET['mode']=='sandbox') {
			$api_addr = 'https://api-sandbox.direct.yandex.ru/v4/json/';
			}
		else {
			$api_addr = 'https://api.direct.yandex.ru/v4/json/';
			}
	
		$this->campaigns = json_decode(file_get_contents($api_addr, 0, $context),true);
		return 1;
	}

	/**
	 * 
	 * @param db_host
	 * @param db_login
	 * @param db_pass
	 * @param db_name
	 */
	public function connectDB($db_host, $db_login, $db_pass, $db_name)
	{
		//<?
	
				//запрос в базу
				$db_query_read	= "SELECT client_id, client_secret, access_token FROM yandex_direct WHERE 1;";	
		
				//открываем базу
				$conn = mysql_connect($db_host, $db_login, $db_pass);
		
				if (!$conn) {
				    echo "Unable to connect to DB: " . mysql_error();
				    return 0;
				}
		
				if (!mysql_select_db($db_name)) {
				    echo "Unable to select mydbname: " . mysql_error();
				    return 0;
				}
		
				//достаем данные
				$mysql_result = mysql_query($db_query_read);
		
				if (!$mysql_result) {
				    echo "Could not successfully run query ($db_query_read) from DB: " . mysql_error();
				    return 0;
				}
		
				if (mysql_num_rows($mysql_result) == 0) {
				    echo "No rows found, nothing to print so am exiting";
				    return 0;
				}
		
				$this->requestData = mysql_fetch_assoc($mysql_result);	
				echo "connected to DB</br>";
				return $conn;
	}

	public function requestToken()
	{
		//<?
		// Идентификатор приложения
			
				$client_id		= $this->requestData["client_id"];
				// Пароль приложения
				$client_secret 	= $this->requestData["client_secret"];
				$db_query_write	= "UPDATE yandex_direct SET access_token =  '%s';";
		
				// Если скрипт был вызван с указанием параметра "code" в URL,
				// то выполняется запрос на получение токена
				if (isset($_GET['code']))
				  {
				    // Формирование параметров (тела) POST-запроса с указанием кода подтверждения
				    $query = array(
				      'grant_type' => 'authorization_code',
				      'code' => $_GET['code'],
				      'client_id' => $client_id,
				      'client_secret' => $client_secret
				    );
				    $query = http_build_query($query);
				 
				    // Формирование заголовков POST-запроса
				    $header = "Content-type: application/x-www-form-urlencoded";
		
				    // Выполнение POST-запроса и вывод результата
				    $opts = array('http' =>
				      array(
				      'method'  => 'POST',
				      'header'  => $header,
				      'content' => $query
				      ) 
				    );
				    $context = stream_context_create($opts);
				    $result = file_get_contents('https://oauth.yandex.ru/token', false, $context);
				    $result = json_decode($result);
		
				    // Токен необходимо сохранить для использования в запросах к API Директа
				    //echo $result->access_token;
				    $db_query_write = sprintf($db_query_write,$result->access_token);
					mysql_query($db_query_write);
					$this->access_token = $result->access_token;
					echo "Token have been updated in DB</br>";
					return 1;
				  }
				else
				  {
					echo "Using old token. To update token click here:<a href=https://oauth.yandex.ru/authorize?response_type=code&client_id=$client_id>'code'</a></br>";
					return 0;
				  }
	}

}
?>