<?php

namespace controller;

class Controller
{
    private static $welcomeMessage = 'Welcome';
    private static $byeMessage = 'Bye bye!';
    private static $welcomeWithCookies = 'Welcome back with cookie';

    private $userStorage;
    private $sessionHandler;
    private $loginValidator;
    private $registrationValidator;
    private $rmValidator;

    private $pageView;
    private $loginView;
    private $registerView;
    private $dateTimeView;
    private $rmCalcView;

    private $userIsLoggedIn;

    private $message = '';
    private $rmMessage = '';
    private $userWantsToRegister = false;
    private $loginErrorMessage = '';
    private $registerErrorMessage = '';


    public function __construct(object $modules)
    {
        $this->userStorage = $modules->userStorage;
        $this->sessionHandler = $modules->sessionHandler;
        $this->loginValidator = $modules->loginValidator;
        $this->registrationValidator = $modules->registrationValidator;
        $this->rmValidator = $modules->rmValidator;

        $this->pageView = $modules->pageView;
        $this->loginView = $modules->loginView;
        $this->registerView = $modules->registerView;
        $this->rmCalcView =  $modules->rmCalcView;
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
        if ($this->loginView->userPressesLoginButton() && !$this->userIsLoggedIn) {
            $this->doLoginAttempt();
        }
        if ($this->loginView->userPressesLogoutButton() && $this->userIsLoggedIn) {
            $this->doLogout();
        }

        if ($this->registerView->userPressesRegisterLink()) {
            $this->userWantsToRegister = true;
        }
        if ($this->registerView->userPressesRegisterButton()) {
            $this->doRegisterAttempt();
        }

        if ($this->rmCalcView->userPressesSubmitButton()) {
            $this->doRMCalcAttempt();
        }
    }

    private function doRenderPage(): void
    {
        $body = '';

        // If user is logged in, don't render the RegisterView.
        if (!$this->userIsLoggedIn) {
            $body .= $this->registerView->getHTML($this->userWantsToRegister, $this->registerErrorMessage);
            $this->resetRegisterMessage();
        }

        // If user wants to register, don't render the LoginView.
        if (!$this->userWantsToRegister) {
            // Gather and display the session message, if there is one.
            if ($this->sessionHandler->sessionMessageIsSet()) {
                $this->message = $this->sessionHandler->getAndResetSessionMessage();
            }

            if ($this->loginView->userIsLoggedInWithCookies()) {
                $this->message = self::$welcomeWithCookies;
             }

            $body .= $this->loginView->getHTML($this->userIsLoggedIn, $this->message);
            $this->resetLoginMessage();
        }

        $rmData = new \model\RMCalcData('', '');
        // If rmCalcData is saved in the session and user is logged in, get it        
        if ($this->sessionHandler->rmDataIsSet() && $this->userIsLoggedIn) {
            $rmData = $this->sessionHandler->getSessionRMData();
        }

        $body .= $this->rmCalcView->getHTML($rmData, $this->rmMessage);

        $body .= $this->dateTimeView->getHTML();

        $this->pageView->echoHTML($body);
    }

    private function doLoginAttempt(): void
    {
        $username = $this->loginView->getPostUsername();
        $password = $this->loginView->getPostPassword();
        $keepLoggedInChecked = $this->loginView->userhasCheckedKeepMeLoggedIn();

        $loginData = new \model\LoginData($username, $password, $keepLoggedInChecked);

        // Check for login errors
        try {
            $this->loginValidator->checkIfUsernameIsEmpty($username);
            $this->loginValidator->checkIfPasswordIsEmpty($password);
        } catch (\model\UsernameIsMissingException | \model\PasswordIsMissingException $e) {
            $this->loginErrorMessage = $e->getMessage();
        }

        // If error message is empty, login is OK and the application proceeds to try and login the user.
        if (strlen($this->loginErrorMessage) == 0) {
            try {
                $this->userStorage->logInUser($loginData);

                $this->sessionHandler->setSessionUser($username);
                $this->sessionHandler->setSessionMessage(self::$welcomeMessage);
                $this->loginView->setUserCookies($username);
                $this->userIsLoggedIn = true;
            } catch (\Exception $e) {
                $this->loginErrorMessage = $e->getMessage();
                $this->message = $this->loginErrorMessage;
            }
        } else {
            $this->message = $this->loginErrorMessage;
        }
    }

    private function doLogout(): void
    {
        // Clear session and cookies for user.
        $this->loginView->clearUserCookies();
        $this->sessionHandler->clearSessionUser();

        $this->userIsLoggedIn = false;
        $this->sessionHandler->setSessionMessage(self::$byeMessage);
    }

    private function doRegisterAttempt(): void
    {
        $username = $this->registerView->getPostUsername();
        $password = $this->registerView->getPostPassword();
        $repeatedPassword = $this->registerView->getPostRepeatedPassword();

        $registerData = new \model\RegisterData($username, $password, $repeatedPassword);

        // Check for registering errors
        try {
            $this->registrationValidator->checkForTooShortUsername($username);
        } catch (\model\TooShortUsernameException $e) {
            $this->registerErrorMessage .= $e->getMessage() . '<br>';
        }
        try {
            $this->registrationValidator->checkForTooShortPassword($password);
        } catch (\model\TooShortPasswordException $e) {
            $this->registerErrorMessage .= $e->getMessage() . '<br>';
        }
        try {
            $this->registrationValidator->checkForUserAlreadyExists($this->userStorage, $username);
        } catch (\model\UserAlreadyExistsException $e) {
            $this->registerErrorMessage .= $e->getMessage() . '<br>';
        }
        try {
            $this->registrationValidator->checkForUsernameContainsInvalidCharacters($username);
        } catch (\model\UsernameContainsInvalidCharactersException $e) {
            $this->registerErrorMessage .= $e->getMessage() . '<br>';
        }
        try {
            $this->registrationValidator->checkForPasswordsDoNotMatch($password, $repeatedPassword);
        } catch (\model\PasswordsDoNotMatchException $e) {
            $this->registerErrorMessage .= $e->getMessage() . '<br>';
        }

        // If error message is empty, registration is OK and the application proceeds to register the new user.
        if (strlen($this->registerErrorMessage) == 0) {
            $this->userStorage->registerUser($registerData);
            $this->sessionHandler->setSessionMessage('Registered new user.');

            header('location: ?');
        }
    }

    private function doRMCalcAttempt(): void
    {
        $weight = $this->rmCalcView->getPostWeight();
        $reps = $this->rmCalcView->getPostReps();

        try {
            $this->rmValidator->checkIfValueIsNumber($weight);
            $this->rmValidator->checkIfValueIsNumber($reps);

            $this->sessionHandler->setSessionRMData($weight, $reps);
        } catch (\Exception $e) {
            $this->rmMessage = $e->getMessage();
        }
    }

    private function resetLoginMessage(): void
    {
        $this->message = '';
    }

    private function resetRegisterMessage(): void
    {
        $this->registerErrorMessage = '';
    }
}
