<?php

namespace model;

class UserStorage
{
    private static $userKey =  __CLASS__ .  "::User";
    // private static $passwordKey =  __CLASS__ .  "::Password";
    // private static $keepLoggedInKey =  __CLASS__ .  "::Keep";
    private static $url = 'UserDatabase.json';

    private $userDatabase;


    public function __construct()
    {
        $this->userDatabase = $this->loadUserDatabaseFromJSON(self::$url);
    }


    public function logInUser(\model\LoginData $loginData): void
    {
        $username = $loginData->username;
        $password = $loginData->password;

        $userToLogin = new User($username, $password);

        $this->checkUserLoginForErrors($userToLogin);
        $this->setSessionUser($userToLogin->getUsername());
    }

    public function checkUserLoginForErrors(\model\User $userToSearchFor): void
    {
        $username = $userToSearchFor->getUsername();
        $password = $userToSearchFor->getPassword();

        foreach ($this->userDatabase as $user) {
            if ($username == $user->username && $password == $user->password) {
                return;
            } else {
                throw new \exception\WrongCredentialsException('Wrong name or password');
            }
        }
    }

    public function registerUser(\model\LoginData $loginData): void
    {
        $username = $loginData->username;
        $password = $loginData->password;

        $userToRegister = new User($username, $password);

        $this->checkUserRegisteringForErrors($userToRegister);

        // $this->saveUserToJSONDatabase($userToRegister);
    }

    public function checkUserRegisteringForErrors(\model\User $userToSearchFor): void
    {
        $username = $userToSearchFor->getUsername();
        $password = $userToSearchFor->getPassword();
        $strippedUsername = strip_tags($username);
		$repeatedPassword = $this->getPostRepeatPassword();

        foreach ($this->userDatabase as $user) {
            if ($username == $user->username) {
                throw new \exception\UserAlreadyExistsException('User exists, pick another username.');
            } 
        }

        if ($username != $strippedUsername) {
			$message .= 'Username contains invalid characters.';
		}
		if ($password != $repeatedPassword) {
			$message .= '<br>Passwords do not match.';
        }
    }

    public function saveUserToJSONDatabase(User $userToBeSaved): void
    {
        array_push($this->userDatabase, $userToBeSaved);

        $this->userDatabase = json_encode($this->userDatabase);
        file_put_contents($this->url, $this->userDatabase, FILE_USE_INCLUDE_PATH);
    }

    public function setSessionUser(string $username): void
    {
        $_SESSION[self::$userKey] = $username;
    }

    public function clearSessionUser(): void
    {
        $_SESSION[self::$userKey] = null;
    }

    public function userIsLoggedInBySession(): bool
    {
        return isset($_SESSION[self::$userKey]);
    }


    private function loadUserDatabaseFromJSON(string $url): array
    {
        $jsonData = file_get_contents($url, 1);
        $userDatabaseObject = json_decode($jsonData);
        return $userDatabaseObject;
    }
}
