<?php

namespace controller;

class Controller
{
    private $userStorage;

    private $pageView;
    private $loginView;
    private $registerView;
    private $dateTimeView;

    private $userIsLoggedIn;
    private $loginMessage;
    private $registerMessage;


    public function __construct(object $modules)
    {
        $this->userStorage = $modules->userStorage;

        $this->pageView = $modules->pageView;
        $this->loginView = $modules->loginView;
        $this->registerView = $modules->registerView;
        $this->dateTimeView = $modules->dateTimeView;

        $this->loginMessage = '';
        $this->registerMessage = '';
        $this->userIsLoggedIn = $this->userStorage->userIsLoggedInBySession();
    }


    public function runApplication(): void
    {
        $this->listenForUserInputs();

        $this->doRenderPage();
    }


    private function listenForUserInputs(): void
    {
        if ($this->loginView->userPressesLoginButton()) {
            $this->doLoginAttempt();
        }
        if ($this->loginView->userPressesLogoutButton()) {
            $this->doLogout();
        }
    }

    private function doRenderPage(): void
    {
        $body = $this->registerView->getHTML();
        $body .= $this->loginView->getHTML($this->userIsLoggedIn, $this->loginMessage);
        $this->resetLoginMessage();
        $body .= $this->dateTimeView->getHTML();

        $this->pageView->echoHTML($body);
    }

    private function doLoginAttempt(): void
    {
        $username = $this->loginView->getPostUsername();
        $password = $this->loginView->getPostPassword();
        $keepLoggedInChecked = $this->loginView->userhasCheckedKeepMeLoggedIn();

        $loginData = new \model\LoginData($username, $password, $keepLoggedInChecked);

        try {
            $this->userStorage->logInUser($loginData);

            // This code executes ONLY if login completed successfully.
            $this->userIsLoggedIn = true;
            $this->loginMessage = 'Welcome';
        } catch (\Exception $e) {
            $this->loginMessage = $e->getMessage();
        }
    }

    private function doLogout(): void
    {
        $this->loginView->clearUserCookies();
        $this->userStorage->clearSessionUser();
    }

    private function resetLoginMessage(): void
    {
        $this->loginMessage = '';
    }
}
