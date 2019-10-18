<?php

namespace view;

class PageView
{
  private static $registerKey = 'register';

  private $request;
  // private $cookie;


  public function __construct()
  {
    $this->request = $_REQUEST;
    // $this->cookie = $_COOKIE;
  }


  public function echoHTML(string $body): void
  {
    $body .= $this->getRegisterLinkHTML($this->userWantsToRegister());

    echo "<!DOCTYPE html>
      <html>
        <head>
          <meta charset='utf-8'>
          <title>df222fx login app</title>
        </head>
        <body>
          <h1>Assignment 3</h1>          
          <div class='container'>
            $body
          </div>
         </body>
      </html>
    ";
  }


  private function getRegisterLinkHTML(bool $userWantsToRegister): string
  {
    if ($userWantsToRegister) {
      return '<a href="?">Back to login</a>';
    } else {
      return '<a href="?register">Register a new user</a>';
    }
  }

  private function userWantsToRegister(): bool
  {
    return isset($this->request[self::$registerKey]);
  }
}
