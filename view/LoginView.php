<?php

namespace view;

class LoginView
{
	const viewID = 'LoginView';

	private static $login = self::viewID . '::Login';
	private static $logout = self::viewID . '::Logout';
	private static $username = self::viewID . '::UserName';
	private static $password = self::viewID . '::Password';
	private static $cookieName = self::viewID . '::CookieName';
	private static $cookiePassword = self::viewID . '::CookiePassword';
	private static $keep = self::viewID . '::KeepMeLoggedIn';
	private static $messageId = self::viewID . 'LoginView::Message';

	private $postUsernameIsMissing = false;
	private $postPasswordIsMissing = false;
	private $wrongUsernameOrPassword = false;
	private $usernameFieldValue = '';



	public function __construct(\model\UserStorage $userStorage)
	{
		$this->userStorage = $userStorage;
	}



	private function setUserCookies(\model\User $userToLogin): void
	{
		$thirtyDays = time() + 60 * 60 * 24 * 30;

		setcookie(self::$cookieName, $userToLogin->getUsername(), $thirtyDays);

		$randString = substr(md5(rand()), 0, 40);

		setcookie(self::$cookiePassword, $randString, $thirtyDays);
	}

	private function setSessionMessage(string $identifier, string $message): string
	{
		if ($_SESSION[$identifier]) {
			$_SESSION[$identifier] = false;
			return $message;
		}
	}



	private function getPostUsername()
	{
		return $_POST[self::$username];
	}

	private function getPostPassword()
	{
		return $_POST[self::$password];
	}



	private function userPressesLoginButton(): bool
	{
		return isset($_POST[self::$login]);
	}

	private function userPressesLogoutButton(): bool
	{
		return isset($_POST[self::$logout]);
	}

	private function userCookieIsSet(): bool
	{
		return isset($_COOKIE[self::$cookieName]) && isset($_COOKIE[self::$cookiePassword]);
	}

	private function postHasUsername(): bool
	{
		return isset($_POST[self::$username]);
	}

	private function sessionMessageFlagIsSet(string $identifier): bool
	{
		return isset($_SESSION[$identifier]);
	}



	public function response(bool $userIsLoggedIn)
	{
		$response = '';
		$message = '';

		if ($this->userCookieIsSet()) {
			$userIsLoggedIn = true;
		}

		if ($userIsLoggedIn) {
			if ($this->userPressesLogoutButton()) {

				$this->userStorage->clearSessionUser();
				$this->userStorage->clearCookieUser();

				$_SESSION['showBye'] = true;
				header('location: ?');
			}

			if ($this->sessionMessageFlagIsSet('showWelcome')) {
				$message = $this->setSessionMessage('showWelcome', 'Welcome');
			} else if ($this->sessionMessageFlagIsSet('showWelcomeCookie')) {
				$message = $this->setSessionMessage('showWelcomeCookie', 'Welcome back with cookie');
			}

			$response = $this->generateLogoutButtonHTML($message);
		} else {

			if ($this->postHasUsername()) {
				$this->usernameFieldValue = $_POST[self::$username];
			}

			if ($this->userPressesLoginButton()) {
				$this->doLoginAttempt(isset($_POST[self::$keep]));

				$message = $this->getLoginMessage();
			}

			if ($this->sessionMessageFlagIsSet('showBye')) {
				$message = $this->setSessionMessage('showBye', 'Bye bye!');
			}

			$response = $this->generateLoginFormHTML($message);
		}
		return $response;
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
