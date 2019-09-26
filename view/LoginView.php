<?php

namespace view;

class LoginView
{
	private static $login = 'LoginView::Login';
	private static $logout = 'LoginView::Logout';
	private static $username = 'LoginView::UserName';
	private static $password = 'LoginView::Password';
	private static $cookieName = 'LoginView::CookieName';
	private static $cookiePassword = 'LoginView::CookiePassword';
	private static $keep = 'LoginView::KeepMeLoggedIn';
	private static $messageId = 'LoginView::Message';

	private $postUsernameIsMissing = false;
	private $postPasswordIsMissing = false;
	private $wrongUsernameOrPassword = false;
	private $usernameFieldValue = '';

	public function __construct(\model\UserStorage $userStorage)
	{
		$this->userStorage = $userStorage;
	}



	private function userPressesLoginButton(): bool
	{
		return isset($_POST[self::$login]);
	}

	private function userPressesLogoutButton(): bool
	{
		return isset($_POST[self::$logout]);
	}



	public function response(bool $userIsLoggedIn)
	{
		$response = '';
		$message = '';

		if ($userIsLoggedIn) {
			if ($this->userPressesLogoutButton()) {
				$this->userStorage->clearSessionUser();

				$_SESSION['showBye'] = true;
				echo "<meta http-equiv='refresh' content='0'>";
			}

			if (isset($_SESSION['showWelcome'])) {
				$message = 'Welcome';
				$_SESSION['showWelcome'] = false;
			}

			$response = $this->generateLogoutButtonHTML($message);
		} else {
			if (isset($_POST[self::$username])) {
				$this->usernameFieldValue = $_POST[self::$username];
			}

			if ($this->userPressesLoginButton()) {
				$this->doLoginAttempt();

				$message = $this->getLoginMessage();
			}

			if (isset($_SESSION['showBye'])) {
				$message = 'Bye bye!';
				$_SESSION['showBye'] = false;
			}

			$response = $this->generateLoginFormHTML($message);
		}
		return $response;
	}



	private function doLoginAttempt()
	{
		$userToLogin = $this->checkLoginForErrors();

		if (isset($userToLogin)) {
			$userToLogin = $this->userStorage->findMatchingUser($userToLogin);

			if (isset($userToLogin)) {
				$this->userStorage->saveSessionUser($userToLogin);
				$_SESSION['showWelcome'] = true;
				echo "<meta http-equiv='refresh' content='0'>";
			} else {
				$this->wrongUsernameOrPassword = true;
			}
		}
	}

	private function checkLoginForErrors()
	{
		$username = $this->getPostUsername();
		$password = $this->getPostPassword();

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



	private function getPostUsername()
	{
		return $_POST[self::$username];
	}

	private function getPostPassword()
	{
		return $_POST[self::$password];
	}




	private function generateLoginFormHTML($message)
	{
		return '
		<form method="post"> 
			<fieldset>
				<legend>Login - enter Username and password</legend>
				<p id="' . self::$messageId . '">' . $message . '</p>
				
				<label for="' . self::$username . '">Username :</label>
				<input type="text" id="' . self::$username . '" name="' . self::$username . '" value="' . $this->usernameFieldValue . '" />

				<label for="' . self::$password . '">Password :</label>
				<input type="password" id="' . self::$password . '" name="' . self::$password . '" />

				<label for="' . self::$keep . '">Keep me logged in  :</label>
				<input type="checkbox" id="' . self::$keep . '" name="' . self::$keep . '" />
				
				<input type="submit" name="' . self::$login . '" value="login" />
			</fieldset>
		</form>
	';
	}

	private function generateLogoutButtonHTML(string $message)
	{
		return '
		<form method="post">
			<p id="' . self::$messageId . '">' . $message . '</p>
			<input type="submit" name="' . self::$logout . '" value="logout"/>
		</form>
	';
	}
}
