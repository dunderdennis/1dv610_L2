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

	private $requestUsernameIsMissing = false;
	private $requestPasswordIsMissing = false;

	public function response()
	{
		$message = '';
		$this->checkLoginRequestForErrors();
		$message = $this->getLoginErrorMessage();

		$response = $this->generateLoginFormHTML($message);
		$response .= $this->generateLogoutButtonHTML($message);

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
					<input type="text" id="' . self::$username . '" name="' . self::$username . '" value="" />

					<label for="' . self::$password . '">Password :</label>
					<input type="password" id="' . self::$password . '" name="' . self::$password . '" />

					<label for="' . self::$keep . '">Keep me logged in  :</label>
					<input type="checkbox" id="' . self::$keep . '" name="' . self::$keep . '" />
					
					<input type="submit" name="' . self::$login . '" value="login" />
				</fieldset>
			</form>
		';
	}

	private function checkLoginRequestForErrors(): void
	{
		$username = $this->getRequestUsername();
		$password = $this->getRequestPassword();

		var_dump($username);
		var_dump($password);

		if (!isset($username)) {
			$this->requestUsernameIsMissing = true;
		}
		if (!isset($password)) {
			$this->requestPasswordIsMissing = true;
		}
	}

	private function getLoginErrorMessage(): string
	{
		$message = '';

		if ($this->requestUsernameIsMissing) {
			$message = 'Username is missing';
		}

		return $message;
	}

	private function getRequestUsername()
	{
		return $_REQUEST[self::$username];
	}

	private function getRequestPassword()
	{
		return $_REQUEST[self::$password];
	}

	private function getGETUsername()
	{
		return $_GET[self::$username];
	}

	private function getGETPassword()
	{
		return $_GET[self::$password];
	}
}
