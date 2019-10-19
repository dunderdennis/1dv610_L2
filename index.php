<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');
ini_set('display_startup_errors', 1);

date_default_timezone_set('Europe/Stockholm');
session_start();

require_once('controller/Controller.php');

require_once('model/UserStorage.php');
require_once('model/User.php');
require_once('model/LoginData.php');
require_once('model/RegisterData.php');

require_once('view/PageView.php');
require_once('view/LoginView.php');
require_once('view/RegisterView.php');
require_once('view/DateTimeView.php');

require_once('exception/UsernameIsMissingException.php');
require_once('exception/PasswordIsMissingException.php');
require_once('exception/TooShortUsernameException.php');
require_once('exception/TooShortPasswordException.php');
require_once('exception/WrongCredentialsException.php');
require_once('exception/UserAlreadyExistsException.php');


try {
    $modules = new stdClass();

    $modules->userStorage = new \model\UserStorage();

    $modules->pageView = new \view\PageView();
    $modules->loginView = new \view\LoginView();
    $modules->registerView = new \view\RegisterView();
    $modules->dateTimeView = new \view\DateTimeView();

    $controller = new \controller\Controller($modules);

    $controller->runApplication();
} catch (Exception $e) {
    echo 'index.php: ' . $e . $e->getMessage(); // Probably change this to like Error later
}
