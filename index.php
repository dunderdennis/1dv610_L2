<?php

//MAKE SURE ERRORS ARE SHOWN... MIGHT WANT TO TURN THIS OFF ON A PUBLIC SERVER
error_reporting(E_ALL);
ini_set('display_errors', 'On');

date_default_timezone_set('Europe/Stockholm');

require_once("Application.php");

$app = new Application();
$app->run();

/* echo 'POST: ';
var_dump($_POST);
echo 'REQUEST: ';
var_dump($_REQUEST);
echo 'GET: ';
var_dump($_GET); */
