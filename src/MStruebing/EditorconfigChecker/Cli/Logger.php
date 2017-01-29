<?php

namespace MStruebing\EditorconfigChecker\Cli;

class Logger
{
    protected $errors = array();

    public function addError($message, $file = null, $lineNumber = null)
    {
        array_push($this->errors, [$lineNumber, $file, $message]);
    }

    public function printErrors()
    {
        foreach ($this->errors as $errorNumber => $error) {
            var_dump($error);
        }
    }

    public function countErrors()
    {
        return count($this->errors);
    }
}
