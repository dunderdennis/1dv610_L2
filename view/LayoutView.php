<?php

namespace view;

class LayoutView
{
  public function __construct($user)
  {
    $this->user = $user;
  }



  private function userWantsToRegister(): bool
  {
    return isset($_REQUEST['register']);
  }



  public function response(bool $isLoggedIn, LoginView $loginView, DateTimeView $dateTimeView, RegisterView $registerView): void
  {
    $viewToDisplay = '';
    if ($this->userWantsToRegister()) {
      $viewToDisplay = $registerView->response();
    } else {
      $viewToDisplay = $loginView->response($isLoggedIn);
      if ($this->userWantsToLogin) { }
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

  private function renderRegisterLink(bool $userWantsToRegister)
  {
    if ($userWantsToRegister) {
      return '<a href="?">Back to login</a>';
    } else {
      return '<a href="?register">Register a new user</a>';
    }
  }

  private function renderIsLoggedIn(bool $isLoggedIn): string
  {
    if ($isLoggedIn) {
      // return '<h2>Logged in as ' . $this->user->getUsername() . '</h2>';
      return '<h2>Logged in</h2>';
    } else {
      return '<h2>Not logged in</h2>';
    }
  }
}
