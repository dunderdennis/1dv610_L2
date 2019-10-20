<?php

namespace view;

class RegisterView
{
	const viewID = 'RegisterView';

	private static $register = 			self::viewID . '::Register';
	private static $username = 			self::viewID . '::UserName';
	private static $password = 			self::viewID . '::Password';
	private static $repeatPassword = 	self::viewID . '::PasswordRepeat';
	private static $messageId = 		self::viewID . '::Message';

	private static $registerRequest = 'register';

	private $message = '';


	public function getHTML(bool $userWantsToRegister, string $message): string
	{
		$ret = $this->getRegisterLinkHTML($userWantsToRegister);

		$this->message = $message;

		if ($userWantsToRegister) {
			// The view retains the entered username if registering fails
			$usernameFieldValue = '';
			if ($this->postHasUsername()) {
				$usernameFieldValue = $this->getPostUsername();
			}
			$ret .= $this->getRegisterFormHTML($message, $usernameFieldValue);
		}

		return $ret;
	}

	public function userPressesRegisterLink(): bool
	{
		return isset($_REQUEST[self::$registerRequest]);
	}

	public function userPressesRegisterButton(): bool
	{
		return isset($_POST[self::$register]);
	}

	public function getPostUsername(): string
	{
		return $_POST[self::$username];
	}

	public function getPostPassword(): string
	{
		return $_POST[self::$password];
	}

	public function getPostRepeatedPassword(): string
	{
		return $_POST[self::$repeatPassword];
	}


	private function postHasUsername(): bool
	{
		return isset($_POST[self::$username]);
	}

	private function getRegisterLinkHTML(bool $userWantsToRegister): string
	{
		if ($userWantsToRegister) {
			return '<a href="?">Back to login</a>';
		} else {
			return '<a href="?' . self::$registerRequest . '">Register a new user</a>';
		}
	}

	private function getRegisterFormHTML(string $message, string $usernameFieldValue): string
	{
		return '
		<form method="post" enctype="multipart/form-data">
		<fieldset>
			<legend>Register a new user - Write username and password</legend>
			<p id="' . self::$messageId . '">' . $message . '</p>
			<br>

			<label for="' . self::$username . '">Username :</label>
			<input type="text" id="' . self::$username . '" name="' . self::$username . '" value="' . $usernameFieldValue . '" />
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
