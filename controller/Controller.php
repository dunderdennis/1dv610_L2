<?php

namespace controller;

class Controller
{
    private $user;
    private $pageView;


    public function __construct(object $modules)
    {
        $this->userStorage = $modules->userStorage;

        $this->pageView = $modules->pageView;
        $this->loginView = $modules->loginView;
        $this->registerView = $modules->registerView;
        $this->dateTimeView = $modules->dateTimeView;
    }


    public function doRenderPage(): void
    {
        $body = '';

        // ' . $this->renderRegisterLink($this->userWantsToRegister()) . '
        // ' . $this->renderIsLoggedIn($isLoggedIn) . '
        //     ' . $viewToDisplay . '
        //     ' . $dateTimeView->show() . '

        $this->pageView->echoHTML($body);
    }


    private function doUserLogin(): void
    {
        if ($this->userWantsToLogin) {
            $this->pageView->logInUser($this->user);
        }
    }

    // if ($this->userWantsToRegister()) {
    //   $viewToDisplay = $registerView->response();
    // } else {
    //   $viewToDisplay = $loginView->response($isLoggedIn);
    //   if ($this->userWantsToLogin) { }
    // }

    private function getIsLoggedInHTML(bool $isLoggedIn): string
    {
        if ($isLoggedIn) {
            return '<h2>Logged in</h2>';
        } else {
            return '<h2>Not logged in</h2>';
        }
    }
}
