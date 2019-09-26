<?php

namespace view;

class RegisterView
{
	private static $register = 'RegisterView::Register';
	private static $username = 'RegisterView::UserName';
	private static $password = 'RegisterView::Password';
	private static $repeatPassword = 'RegisterView::PasswordRepeat';
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

	private function userPressesRegisterButton(): bool
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

	private function getRegisteringErrors(): string
	{
		$message = '';

		if ($this->postUsernameIsMissing) {
			$message .= 'Username is missing<br>';
		}
		if ($this->postUsernameIsTooShort) {
			$message .= 'Username is too short<br>';
		}


		if ($this->postPasswordIsMissing) {
			$message .= 'Password is missing<br>';
		}
		if ($this->postPasswordIsTooShort) {
			$message .= 'Password is too short<br>';
		}
		if ($this->passwordsDoNotMatch) {
			$message .= 'Passwords do not match.';
		}

		return $message;
	}

	private function TryRegisteringNewUser()
	{
		$username = $this->getPostUsername();
		$password = $this->getPostPassword();
		$repeatedPassword = $this->getPostRepeatPassword();

		$newUserOk = true;

		if ($username == '') {
			$this->postUsernameIsMissing = true;
			$newUserOk = false;
		} else if (strlen($username) < self::$minimumUsernameLength) {
			$this->postUsernameIsTooShort = true;
			$newUserOk = false;
		} else {
			$_POST[self::$username] = $username;
		}

		if ($password == '') {
			$this->postPasswordIsMissing = true;
			$newUserOk = false;
		} else if (strlen($password) < self::$minimumPasswordLength) {
			$this->postPasswordIsTooShort = true;
			$newUserOk = false;
		} else if ($password != $repeatedPassword) {
			$this->passwordsDoNotMatch = true;
			$newUserOk = false;
		} else {
			$_POST[self::$username] = $username;
		}
		if ($newUserOk) {
			echo 'CODE EXECUTES HERE';
			$newUser = new \model\User($_POST[self::$username], $_POST[self::$password]);
			$this->userStorage->saveSessionUser($newUser);
			$this->userStorage->saveUserToJSONDatabase($newUser);
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
