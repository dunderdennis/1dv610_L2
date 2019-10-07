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
	private $usernameContainsInvalidCharacters = false;
	private $passwordsDoNotMatch = false;
	private $usernameFieldValue = '';



	public function __construct(\model\UserStorage $userStorage)
	{
		$this->userStorage = $userStorage;
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
			$message .= 'Username is missing';
		}
		if ($this->postUsernameIsTooShort) {
			$message .= 'Username has too few characters, at least 3 characters.';
		}
		if ($this->usernameContainsInvalidCharacters) {
			$message .= 'Username contains invalid characters.';
		}


		if ($this->postPasswordIsMissing) {
			$message .= '<br>Password is missing';
		}
		if ($this->postPasswordIsTooShort) {
			$message .= '<br>Password has too few characters, at least 6 characters.';
		}
		if ($this->passwordsDoNotMatch) {
			$message .= '<br>Passwords do not match.';
		}

		return $message;
	}

	private function TryRegisteringNewUser()
	{
		$username = $this->getPostUsername();
		$strippedUsername = strip_tags($username);
		$password = $this->getPostPassword();
		$repeatedPassword = $this->getPostRepeatPassword();

		$newUserOk = true;

		if ($username == '') {
			$this->postUsernameIsMissing = true;
			$newUserOk = false;
		}
		if (strlen($username) < self::$minimumUsernameLength) {
			$this->postUsernameIsTooShort = true;
			$newUserOk = false;
		}
		if ($username != $strippedUsername) {
			$this->usernameContainsInvalidCharacters = true;
			$newUserOk = false;
		}

		if ($password == '') {
			$this->postPasswordIsMissing = true;
			$newUserOk = false;
		}
		if (strlen($password) < self::$minimumPasswordLength) {
			$this->postPasswordIsTooShort = true;
			$newUserOk = false;
		}
		if ($password != $repeatedPassword) {
			$this->passwordsDoNotMatch = true;
			$newUserOk = false;
		}

		if ($newUserOk) {
			$newUser = new \model\User($username, $password);
			$this->userStorage->saveSessionUser($newUser);
			$this->userStorage->saveUserToJSONDatabase($newUser);

			$homepage = '?';
			$this->pageRedirect($homepage);
		}
	}

	private function pageRedirect($location)
	{
		echo '<META HTTP-EQUIV="Refresh" Content="0; URL=' . $location . '">';
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
			<input type="text" id="' . self::$username . '" name="' . self::$username . '" value="' . strip_tags($this->usernameFieldValue) . '" />
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
}
