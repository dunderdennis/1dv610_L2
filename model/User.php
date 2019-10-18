<?php

namespace model;

class User
{
    private static $minNameLength = 2;
    public $username = null; // Maybe make these private. for now this works GREAT.
    public $password = null;



    public function __construct(string $name, string $password)
    {
        $this->username = $this->applyFilter($name);
        $this->password = $password;

        if (strlen($this->username) < self::$minNameLength) {
            throw new \exception\TooShortNameException('Username needs to be at least 2 characters.');
        }
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



    public static function applyFilter(string $rawInput): string
    {
        return trim(htmlentities($rawInput));
    }
}
