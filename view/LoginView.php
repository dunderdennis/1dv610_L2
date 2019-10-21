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
	private static $keepLoggedIn = 		self::viewID . '::KeepMeLoggedIn';
	private static $messageId = 		self::viewID . '::Message';


	public function getHTML(bool $userIsLoggedIn, string $message): string
	{
		$ret = $this->getLoggedInHTML($userIsLoggedIn);

		$message = $message;

		if ($userIsLoggedIn || $this->userIsLoggedInWithCookies()) {
			$ret .= $this->getLogoutButtonHTML($message);
		} else {
			$usernameFieldValue = '';
			// The view retains the entered username if login fails
			if ($this->postHasUsername()) {
				$usernameFieldValue = $this->getPostUsername();
			}

			$ret .= $this->getLoginFormHTML($message, $usernameFieldValue);
		}

		return $ret;
	}

	public function userIsLoggedInWithCookies(): bool
	{
		return isset($_COOKIE[self::$cookieUsername]) && isset($_COOKIE[self::$cookiePassword]);
	}

	public function getCookieUser(): \model\User
	{
		$username = $this->getCookieUsernameKey;
		$password = $this->getCookiePasswordKey;

		assert(
			isset($username) && isset($password),
			'Cookie keys for username and password must be set in order to call this function.'
		);

		return new \model\User($username, $password);
	}

	public function setUserCookies(string $username): void
	{
		$thirtyDays = time() + 60 * 60 * 24 * 30;

		setcookie(self::$cookieUsername, $username, $thirtyDays);

		$randString = substr(md5(rand()), 0, 40);

		setcookie(self::$cookiePassword, $randString, $thirtyDays);
	}

	public function clearUserCookies(): void
	{
		setcookie(self::$cookieUsername, "", time() - 3600);
		setcookie(self::$cookiePassword, "", time() - 3600);
	}

	public function userPressesLoginButton(): bool
	{
		return isset($_POST[self::$login]);
	}

	public function userhasCheckedKeepMeLoggedIn(): bool
	{
		return isset($_POST[self::$keepLoggedIn]);
	}

	public function getPostUsername(): string
	{
		return $_POST[self::$username];
	}

	public function getPostPassword(): string
	{
		return $_POST[self::$password];
	}

	public function userPressesLogoutButton(): bool
	{
		return isset($_POST[self::$logout]);
	}

	public function postHasUsername(): bool
	{
		return isset($_POST[self::$username]);
	}


	private function getLoggedInHTML(bool $userIsLoggedIn): string
	{
		if ($userIsLoggedIn) {
			return '<h2>Logged in</h2>';
		} else {
			return '<h2>Not logged in</h2>';
		}
	}

	private function getLoginFormHTML(string $message, string $usernameFieldValue): string
	{
		return '
		<form method="post"> 
			<fieldset>
				<legend>Login - enter Username and password</legend>
				<p id="' . self::$messageId . '">' . $message . '</p>
				
				<label for="' . self::$username . '">Username :</label>
				<input type="text" id="' . self::$username . '" name="' . self::$username . '" value="' . $usernameFieldValue . '" />

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
}
