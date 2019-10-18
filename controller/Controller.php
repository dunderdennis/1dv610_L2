<?php

namespace controller;

class Controller
{
    private $userStorage;

    private $pageView;
    private $loginView;
    private $registerView;
    private $dateTimeView;


    public function __construct(object $modules)
    {
        $this->userStorage = $modules->userStorage;

        $this->pageView = $modules->pageView;
        $this->loginView = $modules->loginView;
        $this->registerView = $modules->registerView;
        $this->dateTimeView = $modules->dateTimeView;
    }

    public function runApplication(): void
    {
        $this->listenForUserInputs();

        $this->doRenderPage();
    }


    private function listenForUserInputs()
    {
        if ($this->loginView->userPressesLoginButton()) {
            $this->doLoginAttempt();
        }
    }

    private function doRenderPage(): void
    {
        $body = $this->registerView->getHTML();
        $body .= $this->loginView->getHTML();
        $body .= $this->dateTimeView->getHTML();

        $this->pageView->echoHTML($body);
    }

    private function doLoginAttempt(): void
    {
        try {
            $username = $this->loginView->getPostPassword();
            $password = $this->loginView->getPostUsername();
            $keepLoggedInChecked = $this->loginView->userhasCheckedKeepMeLoggedIn();

            $userToLogin = new \model\User($username, $password);

            $this->checkLoginForErrors($userToLogin);

            if (isset($userToLogin)) {
                $userToLogin = $this->userStorage->findMatchingUser($userToLogin);

                if (isset($userToLogin)) {
                    $this->userStorage->saveSessionUser($userToLogin);
                    $_SESSION['showWelcome'] = true;

                    if ($keepLoggedInChecked) {
                        $this->setUserCookies($userToLogin);
                    }

                    header('location: ?');
                } else {
                    $this->wrongUsernameOrPassword = true;
                }
            }
        } catch (\Exception $e) {
            echo $e . $e->getMessage();
        }
    }

    private function checkLoginForErrors(\model\User $userToCheck)
    {
        $username = $this->loginView->getPostUsername();
        $password = $this->loginView->getPostPassword();

        if ($username == '' && $password == '') {
            $this->postUsernameIsMissing = true;
            $this->postPasswordIsMissing = true;
        } else if ($username == '') {
            $this->postUsernameIsMissing = true;
        } else if ($password == '') {
            $this->postPasswordIsMissing = true;
            $_POST[self::$username] = $username;
        } else {
            return new \model\User($username, $password);
        }
    }

    private function getLoginMessage(): string
    {
        $message = '';

        if ($this->postUsernameIsMissing) {
            $message = 'Username is missing';
        } else if ($this->postPasswordIsMissing) {
            $message = 'Password is missing';
        } else if ($this->wrongUsernameOrPassword) {
            $message = $this->userStorage->getUserErrorMessage();
        } else {
            $message = 'Welcome';
        }

        return $message;
    }

    private function getIsLoggedInHTML(bool $isLoggedIn): string
    {
        if ($isLoggedIn) {
            return '<h2>Logged in</h2>';
        } else {
            return '<h2>Not logged in</h2>';
        }
    }
}
