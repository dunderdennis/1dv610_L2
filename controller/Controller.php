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
    private $message;
    private $userWantsToRegister;
    private $registerMessage;


    public function __construct(object $modules)
    {
        $this->userStorage = $modules->userStorage;

        $this->pageView = $modules->pageView;
        $this->loginView = $modules->loginView;
        $this->registerView = $modules->registerView;
        $this->dateTimeView = $modules->dateTimeView;

        $this->userIsLoggedIn = $this->userStorage->userIsLoggedInBySession();
        $this->message = '';
        $this->userWantsToRegister = false;
        $this->registerMessage = '';
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

        if ($this->registerView->userPressesRegisterLink()) {
            $this->userWantsToRegister = true;
        }
        if ($this->registerView->userPressesRegisterButton()) {
            $this->doRegisterAttempt();
        }
    }

    private function doRenderPage(): void
    {
        $body = '';

        // If user is logged in, don't render the RegisterView.
        if (!$this->userIsLoggedIn) {
            $body .= $this->registerView->getHTML($this->userWantsToRegister, $this->registerMessage);
            $this->resetRegisterMessage();
        }

        // If user wants to register, don't render the LoginView.
        if (!$this->userWantsToRegister) {
            // Gather and display the session message, if there is one.
            if ($this->userStorage->sessionMessageIsSet()) {
                $this->message = $this->userStorage->getAndResetSessionMessage();
            }
            $body .= $this->loginView->getHTML($this->userIsLoggedIn, $this->message);
            $this->resetLoginMessage();
        }

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
            $this->message = 'Welcome';
        } catch (\Exception $e) {
            $this->message = $e->getMessage();
        }
    }

    private function doLogout(): void
    {
        $this->loginView->clearUserCookies();
        $this->userStorage->clearSessionUser();

        header('location: ?');
    }

    private function doRegisterAttempt(): void
    {
        $username = $this->registerView->getPostUsername();
        $password = $this->registerView->getPostPassword();
        $repeatedPassword = $this->registerView->getPostRepeatedPassword();

        $registerData = new \model\RegisterData($username, $password, $repeatedPassword);

        try {
            $this->userStorage->registerUser($registerData);

            // This code executes ONLY if registering completed successfully.
            $this->userStorage->setSessionMessage('Registered new user.');
            header('location: ?');
        } catch (\Exception $e) {
            $this->registerMessage = $e->getMessage();
        }
    }

    private function resetLoginMessage(): void
    {
        $this->message = '';
    }

    private function resetRegisterMessage(): void
    {
        $this->registerMessage = '';
    }
}
