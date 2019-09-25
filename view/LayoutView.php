<?php

namespace view;

class LayoutView
{
  private $username;
  private $userWantsToLogin;

  public function __construct($user)
  {
    $this->user = $user;
    $this->userWantsToLogin = false;
  }

  private function userWantsToRegister(): bool
  {
    return isset($_REQUEST['register']);
  }

  /**
   * Render function for LayoutView class.
   *
   * @param boolean $isLoggedIn
   * @param LoginView $loginView
   * @param DateTimeView $dateTimeView
   * @param RegisterView $registerView
   * @return void
   */
  public function response(bool $isLoggedIn, LoginView $loginView, DateTimeView $dateTimeView, RegisterView $registerView): void
  {
    $viewToDisplay = '';
    if ($this->userWantsToRegister()) {
      $viewToDisplay = $registerView->response();
    } else {
      $viewToDisplay = $loginView->response();
    }

    echo '<!DOCTYPE html>
      <html>
        <head>
          <meta charset="utf-8">
          <title>df222fx login app</title>
        </head>
        <body>
          <h1>Assignment 2</h1>
          ' . $this->renderRegisterLink($this->userWantsToRegister()) . '
          ' . $this->renderIsLoggedIn($isLoggedIn) . '
          
          <div class="container">
              ' . $viewToDisplay . '
              
              ' . $dateTimeView->show() . '
          </div>
         </body>
      </html>
    ';
  }

  /**
   * Handles rendering of Register-link.
   *
   * @param boolean $userWantsToRegister
   * @return string
   */
  private function renderRegisterLink(bool $userWantsToRegister)
  {
    if ($userWantsToRegister) {
      return '<a href="?">Back to login</a>';
    } else {
      return '<a href="?register">Register a new user</a>';
    }
  }

  /**
   * Handles rendering of Logged in-text.
   *
   * @param boolean $isLoggedIn
   * @return string
   */
  private function renderIsLoggedIn(bool $isLoggedIn): string
  {
    if ($isLoggedIn) {
      return '<h2>Logged in as ' . $this->user->getUsername() . '</h2>';
    } else {
      return '<h2>Not logged in</h2>';
    }
  }
}
