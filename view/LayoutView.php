<?php

namespace view;

class LayoutView
{
  public function __construct($username)
  {
    $this->username = $username;
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
  public function render(bool $isLoggedIn, LoginView $loginView, DateTimeView $dateTimeView, RegisterView $registerView)
  {
    echo '<!DOCTYPE html>
      <html>
        <head>
          <meta charset="utf-8">
          <title>df222fx login app</title>
        </head>
        <body>
          <h1>Assignment 2</h1>
          ' . $this->renderIsLoggedIn($isLoggedIn, $loginView) . '
          
          <div class="container">
              ' . $loginView->response() . '
              
              ' . $dateTimeView->show() . '
          </div>
         </body>
      </html>
    ';
  }

  /**
   * Handles rendering of Register-link and Logged in-text.
   *
   * @param boolean $isLoggedIn
   * @return void
   */
  private function renderIsLoggedIn(bool $isLoggedIn)
  {
    if ($isLoggedIn) {
      return '<h2>Logged in as ' . $this->username . '</h2>';
    } else {
      return '<a href="?register">New user? Register HERE</a>
      <h2>Not logged in</h2>';
    }
  }
}
