<?php

// turn this off on public server. for devs
error_reporting(E_ALL);
ini_set('display_errors', 'On');

date_default_timezone_set('Europe/Stockholm');

require_once("Application.php");

$app = new Application();
$app->run();
