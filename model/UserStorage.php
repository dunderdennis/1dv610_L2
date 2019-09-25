<?php

namespace model;

session_start();

class UserStorage
{
    private static $SESSION_KEY =  __CLASS__ .  "::User";

    
    public function loadUser()
    {
        echo 'ACCESSING $_SESSION IN UserStorage:';
        var_dump($_SESSION);
        if (isset($_SESSION[self::$SESSION_KEY])) {
            return $_SESSION[self::$SESSION_KEY];
        } else {
            return null;
        }
    }

    public function saveUser(User $toBeSaved)
    {
        $_SESSION[self::$SESSION_KEY] = $toBeSaved;
    }
}
