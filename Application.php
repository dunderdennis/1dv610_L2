<?php

require_once('controller/UserController.php');
require_once('model/UserName.php');
require_once('model/UserStorage.php');
require_once('view/LoginView.php');
require_once('view/DateTimeView.php');
require_once('view/LayoutView.php');
require_once('view/RegisterView.php');

class Application
{
    private $storage;
    private $username;
    private $controller;

    private $layoutView;
    private $dateTimeView;
    private $loginView;
    private $registerView;

    public function __construct()
    {
        $this->storage = new \model\UserStorage();
        $this->username = $this->storage->loadUser();

        $this->layoutView = new \view\LayoutView($this->username);
        $this->dateTimeView = new \view\DateTimeView();
        $this->loginView = new \view\LoginView();
        $this->registerView = new \view\RegisterView();

        $this->controller = new  \controller\UserController($this->username, $this->layoutView);
    }

    public function run() {
        $this->render();
    }

    public function render()
    {
        $this->layoutView->render(false, $this->loginView, $this->dateTimeView, $this->registerView);
    }
}
