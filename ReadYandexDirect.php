<?php
ini_set("display_errors", "1");
error_reporting(0);
if (isset($_GET['err_rep'])) error_reporting(E_ALL);
require_once ('../config_db.php');
require_once ('ReadYandexDirectClass.php');

//соединяемся с базой, записываем или запрашиваем токен и с токеном запрашиваем результаты
$readYandexDirectObj = New ReadYandexDirectClass;
if ($readYandexDirectObj->connectDB($CONFIG["HostName"],$CONFIG["DBUserName"],$CONFIG["DBPassword"],$CONFIG["DBName"]))
{$readYandexDirectObj->requestToken();
$readYandexDirectObj->requestCampaigns();

// вывод результата
$output = $readYandexDirectObj->campaigns["data"][0];	
echo ":)";	
echo "
<table>
<tbody>
<tr>
<td>Кампания</td>
<td>Бюджет</td>
<td>Показы</td>
<td>CTR</td>
<td>Клики</td>
<td>Цена клика</td>
<td>Конверсия</td>
<td>Лиды</td>
<td>Стоимость лида</td>
<td>Израсходовано</td>
<td>Прибыль</td>
<td>ROI</td></tr>
<tr>
<td>Name<br>
показы с StartDate<br>
Status<br>
StatusShow<br>
StatusArchive<br>
StatusActivating<br>
IsActive<br>
</td>
<td>".$output["Sum"]."</td>
<td>".$output["Shows"]."</td>
<td>".number_format($output["Clicks"]/$output["Shows"]*100,2)." %</td>
<td>".$output["Clicks"]."</td>
<td>?</td>
<td>?</td>
<td>?</td>
<td>?</td>
<td>".$output["Rest"]."</td>
<td>?</td>
<td>?</td>
</tr>
</tbody>
</table>
	";
echo "<br>::)";	
	}

?>