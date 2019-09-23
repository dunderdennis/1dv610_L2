<?php

namespace view;

class LayoutView
{
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
          <title>Login Example</title>
        </head>
        <body>
          <h1>Assignment 2</h1>
          ' . $this->renderIsLoggedIn($isLoggedIn) . '
          
          <div class="container">
              ' . $loginView->response() . '
              
              ' . $dateTimeView->show() . '
          </div>
         </body>
      </html>
    ';
  }

  private function renderIsLoggedIn($isLoggedIn)
  {
    if ($isLoggedIn) {
      return '<h2>Logged in</h2>';
    } else {
      return '<a href="?register">New user? Register HERE</a>
      <h2>Not logged in</h2>';
    }
  }
}
