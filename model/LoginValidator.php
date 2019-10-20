<?php

namespace model;

class LoginValidator
{
    public function checkIfUsernameIsEmpty(string $username): void
    {
        $username = $this->applyFilter($username);

        if ($username == '') {
            throw new \model\UsernameIsMissingException('Username is missing');
        }
    }

    public function checkIfPasswordIsEmpty(string $password): void
    {
        if ($password == '') {
            throw new \model\PasswordIsMissingException('Password is missing');
        }
    }


    private function applyFilter(string $rawInput): string
    {
        return trim(htmlentities($rawInput));
    }
}
