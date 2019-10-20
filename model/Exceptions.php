<?php

namespace model;

class PasswordsDoNotMatchException extends \Exception
{ }

class PasswordIsMissingException extends \Exception
{ }

class TooShortPasswordException extends \Exception
{ }

class TooShortUsernameException extends \Exception
{ }

class UserAlreadyExistsException extends \Exception
{ }

class UsernameContainsInvalidCharactersException extends \Exception
{ }

class UsernameIsMissingException extends \Exception
{ }

class WrongCredentialsException extends \Exception
{ }

class NotANumberException extends \Exception
{ }
