<?php

namespace model;

class User
{
    private static $minUsernameLength = 2;
    private static $minPasswordLength = 6;

    private $username;
    private $password;


    public function __construct(string $username, string $password)
    {
        $this->username = $this->applyFilter($username);
        $this->password = $password;

        $this->checkUserDataForErrors();
    }


    public function setUsername(string $newName): void
    {
        $this->username = $newName;
    }

    public function setPassword(string $newPassword): void
    {
        $this->password = $newPassword;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }


    private function checkUserDataForErrors(): void
    {
        if ($this->username == '') {
            throw new \exception\UsernameIsMissingException('Username is missing');
        } else if ($this->password == '') {
            throw new \exception\PasswordIsMissingException('Password is missing');
        } else if (strlen($this->username) < self::$minUsernameLength) {
            throw new \exception\TooShortUsernameException('Username needs to be at least ' . self::$minUsernameLength . ' characters.');
        } else if (strlen($this->password) < self::$minPasswordLength) {
            throw new \exception\TooShortPasswordException('Password needs to be at least ' . self::$minPasswordLength . ' characters.');
        }
    }

    private function applyFilter(string $rawInput): string
    {
        return trim(htmlentities($rawInput));
    }
}
