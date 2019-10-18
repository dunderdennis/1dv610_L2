<?php

namespace model;

class LoginData
{
    public $username;
    public $password;
    public $keeploggedInChecked;


    public function __construct(string $username, string $password, bool $keeploggedInChecked)
    {
        $this->username = $username;
        $this->password = $password;
        $this->keeploggedInChecked = $keeploggedInChecked;
    }
}
