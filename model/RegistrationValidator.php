<?php

namespace model;

class RegistrationValidator
{
    private static $minUsernameLength = 2;
    private static $minPasswordLength = 6;


    public function checkForTooShortUsername(string $username): void
    {
        if (strlen($username) < self::$minUsernameLength) {
            throw new \model\TooShortUsernameException('Username needs to be at least ' . self::$minUsernameLength . ' characters.');
        }
    }

    public function checkForTooShortPassword(string $password): void
    {
        if (strlen($password) < self::$minPasswordLength) {
            throw new \model\TooShortPasswordException('Password needs to be at least ' . self::$minPasswordLength . ' characters.');
        }
    }

    public function checkForUserAlreadyExists(\model\UserStorage $userStorage, string $username): void
    {
        foreach ($userStorage->getUserDatabase() as $dbUser) {
            if ($username == $dbUser->username) {
                throw new \model\UserAlreadyExistsException('User exists, pick another username.');
            }
        }
    }

    public function checkForUsernameContainsInvalidCharacters(string $username): void
    {
        $strippedUsername = strip_tags($username);
        if ($username != $strippedUsername) {
            throw new \model\UsernameContainsInvalidCharactersException('Username contains invalid characters.');
        }
    }

    public function checkForPasswordsDoNotMatch(string $password, string $repeatedPassword): void
    {
        if ($password != $repeatedPassword) {
            throw new \model\PasswordsDoNotMatchException('Passwords do not match.');
        }
    }
}
