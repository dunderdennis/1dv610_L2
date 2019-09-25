<?php

namespace view;

class RegisterView
{
	private static $login = 'RegisterView::Login';
	private static $logout = 'RegisterView::Logout';
	private static $username = 'RegisterView::UserName';
	private static $password = 'RegisterView::Password';
	private static $cookieName = 'RegisterView::CookieName';
	private static $cookiePassword = 'RegisterView::CookiePassword';
	private static $keep = 'RegisterView::KeepMeLoggedIn';
	private static $messageId = 'RegisterView::Message';

	private $postUsernameIsMissing = false;
	private $postPasswordIsMissing = false;
	private $usernameFieldValue = '';



	public function response()
	{
		$response = '';
		$message = '';

		$response .= $this->generateRegisterFormHTML($message);

		return $response;
	}



	private function generateRegisterFormHTML($message)
	{
		return '
		<form method="post" enctype="multipart/form-data">
		<fieldset>
			<legend>Register a new user - Write username and password</legend>
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
}
