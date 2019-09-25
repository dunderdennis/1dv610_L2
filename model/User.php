<?php

namespace model;

class User
{
    private static $minNameLength = 2;
    private $username = null;
    private $password = null;

    public function __construct(string $newName, string $newPassword)
    {
        $this->username = $this->applyFilter($newName);
        $this->password = $newPassword;

        if (strlen($this->username) < self::$minNameLength) {
            throw new \Exception('Username needs to be at least 2 characters.');
        }
    }

    public function setUsername(string $newName): void
    {
        $this->username = $newName;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setPassword(string $newPassword): void
    {
        $this->password = $newPassword;
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
