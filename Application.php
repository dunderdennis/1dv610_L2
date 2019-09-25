<?php

require_once('controller/UserController.php');
require_once('model/User.php');
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

        $this->layoutView = new \view\LayoutView($this->user);
        $this->dateTimeView = new \view\DateTimeView();
        $this->loginView = new \view\LoginView();
        $this->registerView = new \view\RegisterView();

        $this->controller = new  \controller\UserController($this->user, $this->layoutView);
    }

    public function run()
    {
        $this->render();
    }

    private function isLoggedIn(): bool
    {
        return isset($this->user);
    }

    private function render()
    {
        $this->layoutView->response($this->isLoggedIn(), $this->loginView, $this->dateTimeView, $this->registerView);
    }
}
