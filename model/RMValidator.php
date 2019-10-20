<?php

namespace model;

class RMValidator
{
    public function checkIfValueIsNumber(string $value): void
    {
        if (!is_numeric($value)) {
            throw new \model\NotANumberException('Entered data must be numeric');
        }
    }
}
