<?php

namespace model;

class User
{
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
            throw new \model\UsernameIsMissingException('Username is missing');
        } else if ($this->password == '') {
            throw new \model\PasswordIsMissingException('Password is missing');
        }
    }

    private function applyFilter(string $rawInput): string
    {
        return trim(htmlentities($rawInput));
    }
}
