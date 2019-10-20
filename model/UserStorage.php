<?php

namespace model;

class UserStorage
{
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

        $this->checkUserCredentials($userToLogin);
        $this->setSessionUser($userToLogin->getUsername());
    }

    public function checkUserCredentials(\model\User $userToLogin): void
    {
        $username = $userToLogin->getUsername();
        $password = $userToLogin->getPassword();

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

        $userToRegister = new User($username, $password);

        $this->saveUserToJSONDatabase($userToRegister);
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

    public function getUserDatabase() {
        return $this->userDatabase;
    }


    private function loadUserDatabaseFromJSON(string $url): array
    {
        $jsonData = file_get_contents($url, 1);
        $userDatabaseObject = json_decode($jsonData);
        return $userDatabaseObject;
    }
}
