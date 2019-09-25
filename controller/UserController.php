<?php

namespace controller;

class UserController
{
    private $user;
    private $layoutView;

    public function __construct($user, \view\LayoutView $layoutView)
    {
        $this->user = $user;
        $this->layoutView = $layoutView;
    }

    public function doUserLogin()
    {
        if ($this->layoutView->userWantsToLogin) {
            try {
                $this->layoutView->logInUser($this->user);
            } catch (\Exception $e) { }
        }
    }
}
