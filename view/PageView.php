<?php

namespace view;

class PageView
{
  private static $css = '..\style.css';

  public function echoHTML(string $body): void
  {
    echo "<!DOCTYPE html>
      <html>
        <head>
          <meta charset='utf-8'>
          <title>df222fx login app</title>
          <link rel='stylesheet' type='text/css' href=" . self::$css . ">
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
}
