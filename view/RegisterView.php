<?php

namespace view;

class RegisterView
{
	private static $register = 'RegisterView::Register';
	private static $username = 'RegisterView::UserName';
	private static $password = 'RegisterView::Password';
	private static $repeatPassword = 'RegisterView::repeatPassword';
	private static $cookieName = 'RegisterView::CookieName';
	private static $cookiePassword = 'RegisterView::CookiePassword';
	private static $keep = 'RegisterView::KeepMeLoggedIn';
	private static $messageId = 'RegisterView::Message';

	private static $minimumUsernameLength = 3;
	private static $minimumPasswordLength = 6;

	private $postUsernameIsMissing = false;
	private $postPasswordIsMissing = false;
	private $postPasswordIsTooShort = false;
	private $postUsernameIsTooShort = false;
	private $passwordsDoNotMatch = false;
	private $usernameFieldValue = '';

	public function __construct(\model\UserStorage $userStorage)
	{
		$this->userStorage = $userStorage;
	}

	public function userPressesRegisterButton(): bool
	{
		return isset($_POST[self::$register]);
	}



	public function response()
	{
		$response = '';
		$message = '';

		if (isset($_POST[self::$username])) {
			$this->usernameFieldValue = $_POST[self::$username];
		}

		if ($this->userPressesRegisterButton()) {
			$this->TryRegisteringNewUser();

			$message = $this->getRegisteringErrors();
		}

		$response .= $this->generateRegisterFormHTML($message);

		return $response;
	}

	public function getRegisteringErrors(): string
	{
		$message = '';

		if ($this->postUsernameIsMissing) {
			$message .= 'Username is missing';
		}
		if ($this->postPasswordIsMissing) {
			$message .= 'Password is missing';
		}
		if ($this->postPassword) {
			$message .= 'Password is missing';
		}
		if ($this->postPasswordIsMissing) {
			$message .= 'Password is missing';
		}

		return $message;
	}

	public function TryRegisteringNewUser()
	{
		$username = $this->getPostUsername();
		$password = $this->getPostPassword();
		$repeatedPassword = $this->getPostRepeatPassword();

		$newUserOk = true;

		while ($newUserOk) {
			if ($username == '') {
				$this->postUsernameIsMissing = true;
				$newUserOk = false;
			} else {
				$_POST[self::$username] = $username;
			}
			if (strlen($username) < $this->minimumUsernameLength) {
				$this->postUsernameIsTooShort = true;
				$newUserOk = false;
			}
			if ($password == '') {
				$this->postPasswordIsMissing = true;
				$newUserOk = false;
			}
			if (strlen($password) < $this->minimumPasswordLength) {
				$this->postPasswordIsTooShort = true;
				$newUserOk = false;
			}
			if ($password != $repeatedPassword) {
				$this->passwordsDoNotMatch = true;
				$newUserOk = false;
			}

			$newUser = new \model\User($_POST[self::$username], $_POST[self::$password]);
			$this->userStorage->saveUser($newUser);
			return;
		}
	}



	private function generateRegisterFormHTML($message)
	{
		return '
		<form method="post" enctype="multipart/form-data">
		<fieldset>
			<legend>Register a new user - Write username and password</legend>
			<p id="' . self::$messageId . '">' . $message . '</p>
			<br>

			<label for="' . self::$username . '">Username :</label>
			<input type="text" id="' . self::$username . '" name="' . self::$username . '" value="' . $this->usernameFieldValue . '" />
			<br>

			<label for="' . self::$password . '">Password :</label>
			<input type="password" id="' . self::$password . '" name="' . self::$password . '" />
			<br>

			<label for="' . self::$repeatPassword . '">Repeat password :</label>
			<input type="password" id="' . self::$repeatPassword . '" name="' . self::$repeatPassword . '" />
			<br>

			<input type="submit" name="' . self::$register . '" value="Register" />
		</fieldset>
	</form>
	';
	}

	private function getPostUsername()
	{
		return $_POST[self::$username];
	}

	private function getPostPassword()
	{
		return $_POST[self::$password];
	}

	private function getPostRepeatPassword()
	{
		return $_POST[self::$repeatPassword];
	}
}
