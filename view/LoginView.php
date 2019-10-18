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


	private $request;
	private $post;
	private $get;
	private $cookie;

	private $postUsernameIsMissing = false;
	private $postPasswordIsMissing = false;
	private $wrongUsernameOrPassword = false;
	private $usernameFieldValue = '';


	public function __construct()
	{
		$this->request = $_REQUEST;
		$this->post = $_POST;
		$this->get = $_GET;
		$this->cookie = $_COOKIE;
	}


	public function getHTML($message)
	{
		$ret = $this->getLogoutButtonHTML($message);

		$ret .= $this->getLoginFormHTML($message);

		return $ret;
	}



	public function clearCookieUser()
	{
		$cookieName = 'LoginView::CookieName';
		$cookiePassword = 'LoginView::CookiePassword';

		unset($_COOKIE[$cookieName]);
		setcookie($cookieName, null, -1);

		unset($_COOKIE[$cookiePassword]);
		setcookie($cookiePassword, null, -1);
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

	public function getPostUsername(): string
	{
		return $this->post[self::$username];
	}

	public function getPostPassword(): string
	{
		return $this->post[self::$password];
	}

	public function userPressesLogoutButton(): bool
	{
		return isset($this->post[self::$logout]);
	}

	public function postHasUsername(): bool
	{
		return isset($this->post[self::$username]);
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

	private function getLoginFormHTML(string $message): string
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

	private function getLogoutButtonHTML(string $message): string
	{
		return '
		<form method="post">
			<p id="' . self::$messageId . '">' . $message . '</p>
			<input type="submit" name="' . self::$logout . '" value="logout"/>
		</form>
	';
	}

	private function cookieMessageFlagIsSet(string $identifier):bool
	{
		return isset($this->cookie[$identifier]);
	}

	private function userCookieIsSet(): bool
	{
		return isset($this->cookie[self::$cookieUsername]) && isset($this->cookie[self::$cookiePassword]);
	}
}

	// $userIsLoggedIn = false;
	// 
	// if ($this->userCookieIsSet()) {
	// 	$this->getCookieUserCredentials();
	// 	$userIsLoggedIn = true;
	// }

	// public function loadUserFromCookies(string $cookieName, string $cookiePassword): \model\User
	// {


	//     $userKey = self::$userKey;

	//     // If the user exists in the session object, return it.
	//     if (isset($this->session->$userKey)) {
	//         return $this->session->$userKey;
	//     }

	//     if (isset($_COOKIE[$cookieName]) && isset($_COOKIE[$cookiePassword])) {
	//         $_SESSION['showWelcomeCookie'] = true;
	//         return new User($_COOKIE[$cookieName], $_COOKIE[$cookiePassword]);
	//     } else {
	//         return new User('', '');
	//     }
	// }
