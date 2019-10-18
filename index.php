<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');
ini_set('display_startup_errors', 1);

date_default_timezone_set('Europe/Stockholm');


require_once('controller/Controller.php');

require_once('model/User.php');
require_once('model/UserStorage.php');

require_once('view/PageView.php');
require_once('view/LoginView.php');
require_once('view/RegisterView.php');
require_once('view/DateTimeView.php');


try {
    session_start();
    $modules = new stdClass();

    $modules->userStorage = new \model\UserStorage();

    $modules->pageView = new \view\PageView();
    $modules->dateTimeView = new \view\DateTimeView();
    $modules->loginView = new \view\LoginView();
    $modules->registerView = new \view\RegisterView();

    $controller = new \controller\Controller($modules);

    $controller->doRenderPage();
} catch (Exception $e) {
    echo 'Error:', $e->getMessage();
}
