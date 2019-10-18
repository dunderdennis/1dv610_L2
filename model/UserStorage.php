<?php

namespace model;

class UserStorage
{
    private static $userKey =  __CLASS__ .  "::User";
    private static $url = 'UserDatabase.json';
    private $session;

    private $userDatabase;


    public function __construct()
    {
        $this->session = $_SESSION;

        $this->userDatabase = $this->loadUserDatabaseFromJSON(self::$url);
    }


    public function loadUserDatabaseFromJSON(string $url): array
    {
        $jsonData = file_get_contents($url, 1);
        $userDatabaseObject = json_decode($jsonData);
        return $userDatabaseObject;
    }

    public function loadUserFromCookies(string $cookieName, string $cookiePassword): \model\User
    {


        $userKey = self::$userKey;

        // If the user exists in the session object, return it.
        if (isset($this->session->$userKey)) {
            return $this->session->$userKey;
        } 
        
        if (isset($_COOKIE[$cookieName]) && isset($_COOKIE[$cookiePassword])) {
            $_SESSION['showWelcomeCookie'] = true;
            return new User($_COOKIE[$cookieName], $_COOKIE[$cookiePassword]);
        } else {
            return new User('', '');
        }
    }

    public function clearSessionUser()
    {
        $_SESSION[self::$SESSION_KEY] = null;
    }

    public function clearCookieUser()
    {
        $cookieName = 'LoginView::CookieName';
        $cookiePassword = 'LoginView::CookiePassword';

        unset($_COOKIE[$cookieName]);
        setcookie($cookieName, null, -1);

        unset($_COOKIE[$cookiePassword]);
        setcookie($cookiePassword, null, -1);
    }

    public function saveSessionUser(User $userToBeSaved)
    {
        $_SESSION[self::$SESSION_KEY] = $userToBeSaved;
    }

    public function saveUserToJSONDatabase(User $userToBeSaved)
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
}
