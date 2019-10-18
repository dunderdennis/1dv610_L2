<?php

namespace model;

class User
{
    private static $minNameLength = 2;

    private $username = null;
    private $password = null;


    public function __construct(string $username, string $password)
    {
        $this->checkUserDataForErrors($username, $password);

        $this->username = $this->applyFilter($username);
        $this->password = $password;
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


    private function checkUserDataForErrors(string $username, string $password): void
    {
        if ($this->username == '') {
            throw new \exception\UsernameIsMissingException('Username is missing');
        } else if ($this->password == '') {
            throw new \exception\PasswordIsMissingException('Password is missing');
        } else if (strlen($this->username) < self::$minNameLength) {
            throw new \exception\TooShortUsernameException('Username needs to be at least 2 characters.');
        }
    }

    private function applyFilter(string $rawInput): string
    {
        return trim(htmlentities($rawInput));
    }
}
