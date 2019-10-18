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

        if ($this->loginView->userPressesLoginButton()) {
            $keepLoggedInChecked = $this->loginView->userhasCheckedKeepMeLoggedIn();
            $this->doLoginAttempt($keepLoggedInChecked);
        }

        $body .= $this->loginView->getHTML();

        // ' . $this->renderRegisterLink($this->userWantsToRegister()) . '
        // ' . $this->renderIsLoggedIn($isLoggedIn) . '
        //     ' . $viewToDisplay . '
        //     ' . $dateTimeView->show() . '

        $this->pageView->echoHTML($body);
    }

    private function doLoginAttempt(bool $keepLoggedInChecked)
    {
        $userToLogin = $this->checkLoginForErrors();

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
    }

    private function checkLoginForErrors()
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
