<?php

namespace MStruebing\EditorconfigChecker\Cli;

class Logger
{
    protected $errors = array();

    public function addError($message, $file = null, $lineNumber = null)
    {
        array_push($this->errors, ["lineNumber" => $lineNumber, "file" => $file, "message" => $message]);
    }

    public function printErrors()
    {
        foreach ($this->errors as $errorNumber => $error) {
            printf("Error #%d" . PHP_EOL, $errorNumber);
            printf("\t %s" . PHP_EOL, $error['message']);
            printf("\t on line %d" . PHP_EOL, $error['lineNumber']);
            printf("\t in file %s" . PHP_EOL, $error['file']);
            printf(PHP_EOL);
        }

        printf('%d errors occured' . PHP_EOL, $this->countErrors());
        printf('Check log above and fix the issues.' . PHP_EOL);
    }

    public function countErrors()
    {
        return count($this->errors);
    }
}
