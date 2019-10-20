<?php

require_once('controller/Controller.php');

require_once('model/UserStorage.php');
require_once('model/User.php');
require_once('model/LoginData.php');
require_once('model/RegisterData.php');
require_once('model/SessionHandler.php');
require_once('model/RegistrationValidator.php');
require_once('model/LoginValidator.php');
require_once('model/Exceptions.php');

require_once('view/PageView.php');
require_once('view/LoginView.php');
require_once('view/RegisterView.php');
require_once('view/DateTimeView.php');


class Application
{
    private $modules;
    private $controller;


    public function __construct()
    {
        // Store all the modules of the application in an object
        $this->modules = new \stdClass();

        $this->modules->userStorage = new \model\UserStorage();
        $this->modules->sessionHandler = new \model\SessionHandler();
        $this->modules->loginValidator = new \model\LoginValidator();
        $this->modules->registrationValidator = new \model\RegistrationValidator();

        $this->modules->pageView = new \view\PageView();
        $this->modules->loginView = new \view\LoginView();
        $this->modules->registerView = new \view\RegisterView();
        $this->modules->dateTimeView = new \view\DateTimeView();

        // Pass on the modules object, to avoid having this line being far too long
        $this->controller = new \controller\Controller($this->modules);
    }


    public function run()
    {
        $this->controller->run();
    }
}
