<?php

require_once('controller/UserManagement.php');
require_once('model/UserName.php.php');
require_once('model/UserStorage.php');
require_once('view/LoginView.php');
require_once('view/DateTimeView.php');
require_once('view/LayoutView.php');
require_once('view/RegisterView.php');

class Application
{
    private $storage;
    private $user;
    private $controller;

    private $layoutView;
    private $dateTimeView;
    private $loginView;
    private $registerView;

    public function __construct()
    {
        $this->storage = new \model\UserStorage();
        $this->user = $this->storage->loadUser();

        $this->layoutView = new LayoutView();
        $this->dateTimeView = new DateTimeView();
        $this->loginView = new LoginView();
        $this->registerView = new RegisterView();

        $this->controller = new UserManagement();
    }

    public function render()
    {
        $this->layoutView->render(false, $this->loginView, $this->dateTimeView, $this->registerView);
    }
}
