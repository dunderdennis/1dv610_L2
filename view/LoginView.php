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

	public function userWantsToLogin(): bool
	{
		return isset($_POST[self::$login]);
	}

	public function response()
	{
		/* var_dump(isset($_POST));
		var_dump(isset($_POST[self::$login]));

		echo 'POST: ';
		var_dump(isset($_POST));
		echo 'REQUEST: ';
		var_dump(isset($_REQUEST));
		echo 'GET: ';
		var_dump(isset($_GET)); */

		$message = '';

		if ($this->userWantsToLogin()) {
			$this->doLoginAttempt();
			$message = $this->getLoginErrorMessage();
		}

		$response = $this->generateLoginFormHTML($message);
		// $response .= $this->generateLogoutButtonHTML($message);

		return $response;
	}

	private function generateLogoutButtonHTML(string $message)
	{
		return '
			<form  method="post" >
				<p id="' . self::$messageId . '">' . $message . '</p>
				<input type="submit" name="' . self::$logout . '" value="logout"/>
			</form>
		';
	}

	private function generateLoginFormHTML($message)
	{
		return '
			<form method="post" > 
				<fieldset>
					<legend>Login - enter Username and password</legend>
					<p id="' . self::$messageId . '">' . $message . '</p>
					
					<label for="' . self::$username . '">Username :</label>
					<input type="text" id="' . self::$username . '" name="' . self::$username . '" value="' . $_POST['username'] . '" />

					<label for="' . self::$password . '">Password :</label>
					<input type="password" id="' . self::$password . '" name="' . self::$password . '" />

					<label for="' . self::$keep . '">Keep me logged in  :</label>
					<input type="checkbox" id="' . self::$keep . '" name="' . self::$keep . '" />
					
					<input type="submit" name="' . self::$login . '" value="login" />
				</fieldset>
			</form>
		';
	}

	private function doLoginAttempt(): void
	{
		$this->checkLoginForErrors();
	}

	private function checkLoginForErrors(): void
	{
		$username = $this->getPostUsername();
		$password = $this->getPostPassword();

		if ($username == '') {
			$this->postUsernameIsMissing = true;
		} else {
			$_POST['username'] = $username;
		}
		
		if ($password == '') {
			$this->postPasswordIsMissing = true;
		}
	}

	private function getLoginErrorMessage(): string
	{
		$message = '';

		if ($this->postUsernameIsMissing) {
			$message = 'Username is missing';
		}
		if ($this->postPasswordIsMissing) {
			$message = 'Password is missing';
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
}
