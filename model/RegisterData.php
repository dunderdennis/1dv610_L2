<?php

namespace model;

class RegisterData
{
    public $username;
    public $password;
    public $repeatedPassword;


    public function __construct(string $username, string $password, string $repeatedPassword)
    {
        $this->username = $username;
        $this->password = $password;
        $this->repeatedPassword = $repeatedPassword;
    }
}
