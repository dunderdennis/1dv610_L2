<?php

namespace model;

class UserStorage
{
    private static $userKey =  __CLASS__ .  "::User";
    private static $passwordKey =  __CLASS__ .  "::Password";
    private static $keepLoggedInKey =  __CLASS__ .  "::Keep";

    private static $url = 'UserDatabase.json';

    private $session;
    private $userDatabase;


    public function __construct()
    {
        $this->session = $_SESSION;

        $this->userDatabase = $this->loadUserDatabaseFromJSON(self::$url);
    }

    public function logInUser(\model\LoginData $loginData): void
    {
        $username = $loginData->username;
        $password = $loginData->password;

        $userToLogin = new User($username, $password);
    }

    public function clearSessionUser(): void
    {
        $this->session[self::$SESSION_KEY] = null;
    }

    public function saveSessionUser(\model\User $userToBeSaved): void
    {
        $this->session[self::$SESSION_KEY] = $userToBeSaved;
    }

    public function saveUserToJSONDatabase(User $userToBeSaved): void
    {
        array_push($this->userDatabase, $userToBeSaved);

        $this->userDatabase = json_encode($this->userDatabase);
        file_put_contents($this->url, $this->userDatabase, FILE_USE_INCLUDE_PATH);
    }

    public function findMatchingUser(\model\User $userToSearchFor): \model\User
    {
        $username = $userToSearchFor->getUsername();
        $password = $userToSearchFor->getPassword();

        foreach ($this->userDatabase as $key => $value) {
            if ($username == $value->username) {
                if ($password == $value->password) {
                    return $userToSearchFor;
                } else {
                    $this->userErrorMessage = 'Wrong name or password';
                }
            } else {
                $this->userErrorMessage = 'Wrong name or password';
            }
        }
        return new \model\User('', '');
    }

    public function getUserErrorMessage(): string
    {
        return $this->userErrorMessage;
    }

    private function loadUserDatabaseFromJSON(string $url): array
    {
        $jsonData = file_get_contents($url, 1);
        $userDatabaseObject = json_decode($jsonData);
        return $userDatabaseObject;
    }
}
