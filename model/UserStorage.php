<?php

namespace model;

class UserStorage // kanske lyfta ut SessionHandler
{
    private static $minUsernameLength = 2;
    private static $minPasswordLength = 6;

    private static $userKey =  __CLASS__ .  "::User";
    private static $messageKey =  __CLASS__ .  "::Message";
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

        foreach ($this->userDatabase as $dbUser) {
            if ($username == $dbUser->username && $password == $dbUser->password) {
                return; // If user credentials is correct, return from this method.
            }
        }
        throw new \model\WrongCredentialsException('Wrong name or password');
    }

    public function registerUser(\model\RegisterData $registerData): void
    {
        $username = $registerData->username;
        $password = $registerData->password;
        $repeatedPassword = $registerData->repeatedPassword;

        // Error handling, through instancing a User and calling the "checkUser..."-method.
        $userToRegister = new User($username, $password);
        $this->checkUserRegisteringForErrors($registerData);

        $this->saveUserToJSONDatabase($userToRegister);
    }

    public function checkUserRegisteringForErrors(\model\RegisterData $registerData): void
    {
        $username = $registerData->username;
        $password = $registerData->password;
        $repeatedPassword = $registerData->repeatedPassword;

        $strippedUsername = strip_tags($username);

        // Check if a user with the same username already exists
        foreach ($this->userDatabase as $dbUser) {
            if ($username == $dbUser->username) {
                throw new \model\UserAlreadyExistsException('User exists, pick another username.');
            }
        }

        if (strlen($username) < self::$minUsernameLength) {
            throw new \model\TooShortUsernameException('Username needs to be at least ' . self::$minUsernameLength . ' characters.');
        }
        if (strlen($password) < self::$minPasswordLength) {
            throw new \model\TooShortPasswordException('Password needs to be at least ' . self::$minPasswordLength . ' characters.');
        }
        if ($username != $strippedUsername) {
            throw new \model\UsernameContainsInvalidCharactersException('Username contains invalid characters.');
        }
        if ($password != $repeatedPassword) {
            throw new \model\PasswordsDoNotMatchException('Passwords do not match.');
        }
    }

    public function saveUserToJSONDatabase(User $userToBeSaved): void
    {
        // Convert User-object into an anonymous object, in order to save it as JSON
        $userObject = new \stdClass();
        $userObject->username = $userToBeSaved->getUsername();
        $userObject->password = $userToBeSaved->getPassword();

        array_push($this->userDatabase, $userObject);

        $this->userDatabase = json_encode($this->userDatabase);

        file_put_contents(self::$url, $this->userDatabase, FILE_USE_INCLUDE_PATH);
    }

    public function setSessionMessage(string $message): void
    {
        $_SESSION[self::$messageKey] = $message;
    }

    public function sessionMessageIsSet(): bool
    {
        return isset($_SESSION[self::$messageKey]);
    }

    public function getAndResetSessionMessage(): string
    {
        $ret = $_SESSION[self::$messageKey];
        $_SESSION[self::$messageKey] = null;
        return $ret;
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
