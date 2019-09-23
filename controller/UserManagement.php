<?php

class UserManagement
{
    private $view;
    private $username;

    public function __construct(\model\Username $username, \view\LayoutView $view) {
        $this->username = $username;
        $this->view = $view;
    }

    public function doSomething() {}
 }
