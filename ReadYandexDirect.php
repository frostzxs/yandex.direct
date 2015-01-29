<?php
ini_set("display_errors", "1");
error_reporting('E_ALL');
require_once ('../config_db.php');
require_once ('ReadYandexDirectClass.php');

//соединяемся с базой
$readYandexDirectObj = New ReadYandexDirectClass;
if ($readYandexDirectObj->connectDB($CONFIG["HostName"],$CONFIG["DBUserName"],$CONFIG["DBPassword"],$CONFIG["DBName"]))
{$readYandexDirectObj->requestToken();
$readYandexDirectObj->requestCampaigns();}
?>