<?php

namespace model;

class SessionHandler
{
    private static $userKey =  __CLASS__ .  "::User";
    private static $messageKey =  __CLASS__ .  "::Message";

    public function setSessionMessage(string $message): void
    {
        $_SESSION[self::$messageKey] = $message;
    }

    public function sessionMessageIsSet(): bool
    {
        return isset($_SESSION[self::$messageKey]);
    }

    public function getAndResetSessionMessage(): string
    {
        $ret = $_SESSION[self::$messageKey];
        unset($_SESSION[self::$messageKey]);
        return $ret;
    }

    public function setSessionUser(string $username): void
    {
        $_SESSION[self::$userKey] = $username;
    }

    public function clearSessionUser(): void
    {
        $_SESSION[self::$userKey] = null;
    }

    public function userIsLoggedInBySession(): bool
    {
        return isset($_SESSION[self::$userKey]);
    }
}
