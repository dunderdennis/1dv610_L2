<?php

namespace model;

class SessionHandler
{
    private static $userKey =  __CLASS__ .  "::User";
    private static $messageKey =  __CLASS__ .  "::Message";
    private static $weightKey = __CLASS__ . "::Weight";
    private static $repsKey = __CLASS__ . "::Reps";

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

    public function getSessionUser(): string
    {
        return $_SESSION[self::$userKey];
    }

    public function sessionUserIsSet(): bool
    {
        return isset($_SESSION[self::$userKey]);
    }

    public function clearSessionUser(): void
    {
        $_SESSION[self::$userKey] = null;
    }

    public function userIsLoggedInBySession(): bool
    {
        return isset($_SESSION[self::$userKey]);
    }

    public function setSessionRMData(string $weight, string $reps): void
    {
        $_SESSION[$this->getSessionUser() . self::$weightKey] = $weight;
        $_SESSION[$this->getSessionUser() . self::$repsKey] = $reps;
    }

    public function getSessionRMData(): \model\RMCalcData
    {
        return new \model\RMCalcData($_SESSION[$this->getSessionUser() . self::$weightKey], $_SESSION[$this->getSessionUser() . self::$repsKey]);
    }

    public function rmDataIsSet(): bool
    {
        if ($this->sessionUserIsSet()) {
            return isset($_SESSION[$this->getSessionUser() . self::$weightKey]) && isset($_SESSION[$this->getSessionUser() . self::$repsKey]);
        } else {
            return false;
        }
    }

    public function clearRMData(): void
    {
        unset($_SESSION[$this->getSessionUser() . self::$weightKey]);
        unset($_SESSION[$this->getSessionUser() . self::$repsKey]);
    }
}
