<?php

namespace model;

class UserName
{
    private static $minNameLength = 2;
    private $username = null;

    public function __construct(string $newName)
    {

        $this->username = $this->applyFilter($newName);

        if (strlen($this->username) < self::$minNameLength) {
            throw new TooShortNameException();
        }
    }

    public function setName(UserName $newName)
    {
        $this->username = $newName->getUserName();
    }

    public function getUserName()
    {
        return $this->username;
    }

    public function hasUserName(): bool
    {
        return $this->username != null;
    }

    public static function applyFilter(string $rawInput): string
    {
        return trim(htmlentities($rawInput));
    }
}
