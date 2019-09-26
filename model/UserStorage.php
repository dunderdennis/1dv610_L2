<?php

namespace model;

use stdClass;

session_start();

class UserStorage
{
    private static $SESSION_KEY =  __CLASS__ .  "::User";

    private $userDatabase;
    private $userErrorMessage;
    private $url;

    public function __construct()
    {
        $this->url = 'UserDatabase.json';
        $this->userDatabase = $this->loadUserDatabaseFromJSON($this->url);
    }

    public function loadUserDatabaseFromJSON($url)
    {
        $data = file_get_contents($url, 1);
        $phpObj = json_decode($data);
        return $phpObj;
    }

    public function loadSessionUser()
    {
        if (isset($_SESSION[self::$SESSION_KEY])) {
            return $_SESSION[self::$SESSION_KEY];
        } else {
            return null;
        }
    }

    public function clearSessionUser()
    {
        $_SESSION[self::$SESSION_KEY] = null;
    }

    public function saveSessionUser(User $userToBeSaved)
    {
        $_SESSION[self::$SESSION_KEY] = $userToBeSaved;
    }


    public function saveUserToJSONDatabase(User $userToBeSaved)
    {
        array_push($this->userDatabase, $userToBeSaved);
        var_dump($userToBeSaved);
        $this->userDatabase = json_encode($this->userDatabase);
        file_put_contents($this->url, $this->userDatabase, FILE_USE_INCLUDE_PATH);
    }

    public function findMatchingUser(\model\User $userToSearchFor)
    {
        $username = $userToSearchFor->getUsername();
        $password = $userToSearchFor->getPassword();

        foreach ($this->userDatabase as $key => $value) {
            // echo 'KEY & VALUE';
            // var_dump($key);
            // var_dump($value);

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
        return null;
    }

    public function getUserErrorMessage(): string
    {
        return $this->userErrorMessage;
    }
}
