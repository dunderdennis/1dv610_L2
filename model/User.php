<?php

namespace model;

class User
{
    private $username;
    private $password;


    public function __construct(string $username, string $password)
    {
        $this->username = $username;
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
}
