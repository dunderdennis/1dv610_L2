<?php

namespace view;

class LoginView
{
	const viewID = 'LoginView';

	private static $login = 			self::viewID . '::Login';
	private static $logout = 			self::viewID . '::Logout';
	private static $username = 			self::viewID . '::UserName';
	private static $password = 			self::viewID . '::Password';
	private static $cookieUsername = 	self::viewID . '::CookieName';
	private static $cookiePassword = 	self::viewID . '::CookiePassword';
	private static $keepLoggedIn = 				self::viewID . '::KeepMeLoggedIn';
	private static $messageId = 		self::viewID . '::Message';


	private $cookie;
	private $session;
	private $post;

	private $postUsernameIsMissing = false;
	private $postPasswordIsMissing = false;
	private $wrongUsernameOrPassword = false;
	private $usernameFieldValue = '';


	public function __construct()
	{
		$this->cookie = $_COOKIE;
		$this->session = $_SESSION;
		$this->post = $_POST;
	}


	public function getHTML()
	{
		$ret = '';
		$message = '';

		$userIsLoggedIn = false;

		if ($this->userCookieIsSet()) {
			$this->getCookieUserCredentials();
			$userIsLoggedIn = true;
		}

		if ($userIsLoggedIn) {
			if ($this->userPressesLogoutButton()) {

				$this->userStorage->clearSessionUser();
				$this->userStorage->clearCookieUser();

				$_SESSION['showBye'] = true;
				header('location: ?');
			}

			if ($this->cookieMessageFlagIsSet('showWelcome')) {
				$message = $this->setSessionMessage('showWelcome', 'Welcome');
			} else if ($this->cookieMessageFlagIsSet('showWelcomeCookie')) {
				$message = $this->setSessionMessage('showWelcomeCookie', 'Welcome back with cookie');
			}

			$ret = $this->generateLogoutButtonHTML($message);
		} else {

			if ($this->postHasUsername()) {
				$this->usernameFieldValue = $_POST[self::$username];
			}

			if ($this->userPressesLoginButton()) {
				$this->doLoginAttempt(isset($_POST[self::$keep]));

				$message = $this->getLoginMessage();
			}

			if ($this->cookieMessageFlagIsSet('showBye')) {
				$message = $this->setSessionMessage('showBye', 'Bye bye!');
			}

			$ret = $this->generateLoginFormHTML($message);
		}
		return $ret;
	}

	public function getCookieUserCredentials(): \model\User
	{
		$username = $this->getCookieUsernameKey;
		$password = $this->getCookiePasswordKey;

		assert(
			isset($username) && isset($password),
			'Cookie keys for username and password must be set in order to call this function.'
		);

		return new \model\User($username, $password);
	}

	public function userPressesLoginButton(): bool
	{
		return isset($this->post[self::$login]);
	}

	public function userhasCheckedKeepMeLoggedIn(): bool
	{
		return isset($this->post[self::$keepLoggedIn]);
	}

	private function setUserCookies(\model\User $userToLogin): void
	{
		$thirtyDays = time() + 60 * 60 * 24 * 30;

		setcookie(self::$cookieUsername, $userToLogin->getUsername(), $thirtyDays);

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

				<label for="' . self::$keepLoggedIn . '">Keep me logged in  :</label>
				<input type="checkbox" id="' . self::$keepLoggedIn . '" name="' . self::$keepLoggedIn . '" />
				
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

	private function cookieMessageFlagIsSet(string $identifier)
	{
		return isset($this->cookie[$identifier]);
	}

	private function userCookieIsSet(): bool
	{
		return isset($this->cookie[self::$cookieUsername]) && isset($this->cookie[self::$cookiePassword]);
	}

	private function getPostUsername()
	{
		return $this->post[self::$username];
	}

	private function getPostPassword()
	{
		return $this->post[self::$password];
	}

	private function userPressesLogoutButton(): bool
	{
		return isset($this->post[self::$logout]);
	}

	private function postHasUsername(): bool
	{
		return isset($this->post[self::$username]);
	}
}
