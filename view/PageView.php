<?php

namespace view;

class PageView
{
  public function echoHTML(string $body): void
  {
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
}
