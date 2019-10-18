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

            $loginData = new \model\LoginData($username, $password, $keepLoggedInChecked);

            $loginOK = $this->userStorage->checkUserDataIsOK($loginData);




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

    private function getIsLoggedInHTML(bool $isLoggedIn): string
    {
        if ($isLoggedIn) {
            return '<h2>Logged in</h2>';
        } else {
            return '<h2>Not logged in</h2>';
        }
    }
}
