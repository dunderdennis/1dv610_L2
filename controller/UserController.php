<?php

namespace controller;

class UserController
{
    private $username;
    private $layoutView;

    public function __construct(\model\Username $username, \view\LayoutView $layoutView)
    {
        $this->username = $username;
        $this->layoutView = $layoutView;
    }

    public function doUserLogin()
    {
        if ($this->layoutView->userWantsToLogin) {
            try {
                $this->layoutView->logInUser($this->username);
            } catch (\Exception $e) { }
        }
    }
}
