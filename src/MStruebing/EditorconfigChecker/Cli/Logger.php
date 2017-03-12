<?php

namespace MStruebing\EditorconfigChecker\Cli;

class Logger
{
    /**
     * @var array
     */
    protected $errors = array();

    /**
     * @var Logger
     */
    protected static $instance = null;

    /**
     * Returns an instance of this class
     * If there is already an instance this would be returned
     * elsewise a new one is created
     *
     * @return Logger
     */
    public static function getInstance()
    {
        if (!isset(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * Private constructor so no one else can access it
     */
    protected function __construct()
    {
    }

    /**
     * Adds an error to the logger
     *
     * @param string $message
     * @param string $file
     * @param int $lineNumber
     */
    public function addError($message, $file = null, $lineNumber = null)
    {
        array_push($this->errors, ["lineNumber" => $lineNumber, "file" => $file, "message" => $message]);
    }

    /**
     * Prints the errors from the logger to stdout
     */
    public function printErrors()
    {
        foreach ($this->errors as $errorNumber => $error) {
            printf("Error #%d" . PHP_EOL, $errorNumber);
            printf("\t %s" . PHP_EOL, $error['message']);
            if (isset($error['lineNumber'])) {
                printf("\t on line %d" . PHP_EOL, $error['lineNumber']);
            }
            if (isset($error['file'])) {
                printf("\t in file %s" . PHP_EOL, $error['file']);
            }
            printf(PHP_EOL);
        }

        printf('%d errors occured' . PHP_EOL, $this->countErrors());
        printf('Check log above and fix the issues.' . PHP_EOL);
    }

    /**
     * Counts the errors which were added to the logger
     *
     * @return int
     */
    public function countErrors()
    {
        return count($this->errors);
    }
}
