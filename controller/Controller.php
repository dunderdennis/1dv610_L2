<?php

namespace controller;

class Controller
{
    private $userStorage;
    private $sessionHandler;
    private $registrationValidator;

    private $pageView;
    private $loginView;
    private $registerView;
    private $dateTimeView;

    private $userIsLoggedIn;

    private $message = '';
    private $userWantsToRegister = false;
    private $registerMessage = '';


    public function __construct(object $modules)
    {
        $this->userStorage = $modules->userStorage;
        $this->sessionHandler = $modules->sessionHandler;
        $this->registrationValidator = $modules->registrationValidator;
        
        $this->pageView = $modules->pageView;
        $this->loginView = $modules->loginView;
        $this->registerView = $modules->registerView;
        $this->dateTimeView = $modules->dateTimeView;

        $this->userIsLoggedIn = $this->sessionHandler->userIsLoggedInBySession();
    }


    public function run(): void
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
            if ($this->sessionHandler->sessionMessageIsSet()) {
                $this->message = $this->sessionHandler->getAndResetSessionMessage();
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

        // Try for and catch login errors
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
        // Clear session and cookies for user.
        $this->loginView->clearUserCookies();
        $this->userStorage->clearSessionUser();

        header('location: ?'); // Re-render the page after the application has updated its data.
    }

    private function doRegisterAttempt(): void
    {
        $username = $this->registerView->getPostUsername();
        $password = $this->registerView->getPostPassword();
        $repeatedPassword = $this->registerView->getPostRepeatedPassword();

        $registerData = new \model\RegisterData($username, $password, $repeatedPassword);

        // Try for and catch registering errors
        try { $this->registrationValidator->checkForTooShortUsername($username); } catch (\Exception $e) {
            $this->registerMessage .= $e->getMessage();
        }
        try { $this->registrationValidator->checkForTooShortPassword($password); } catch (\Exception $e) {
            $this->registerMessage .= $e->getMessage();
        }
        try { $this->registrationValidator->checkForUserAlreadyExists($this->userStorage, $username); } catch (\Exception $e) {
            $this->registerMessage .= $e->getMessage();
        }
        try { $this->registrationValidator->checkForUsernameContainsInvalidCharacters($username); } catch (\Exception $e) {
            $this->registerMessage .= $e->getMessage();
        }
        try { $this->registrationValidator->checkForPasswordsDoNotMatch($password, $repeatedPassword); } catch (\Exception $e) {
            $this->registerMessage .= $e->getMessage();
        }


        $this->userStorage->registerUser($registerData);
        $this->sessionHandler->setSessionMessage('Registered new user.');
        header('location: ?');
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
