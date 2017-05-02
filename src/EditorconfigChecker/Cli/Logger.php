<?php

namespace EditorconfigChecker\Cli;

class Logger
{
    /**
     * @var array
     */
    protected $errors = array();

    protected $lines = 0;

    protected $files = 0;

    /**
     * @var Logger
     */
    protected static $instance = null;

    /**
     * Returns an instance of this class
     * If there is already an instance this would be returned
     * else wise a new one is created
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
     * Protected constructor so no one else can access it
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
    public function addError($message, $fileName = null, $lineNumber = null)
    {
        array_push($this->errors, ['lineNumber' => $lineNumber, 'fileName' => $fileName, 'message' => $message]);
    }

    /**
     * Prints the errors from the logger to stdout
     */
    public function printErrors()
    {
        foreach ($this->errors as $errorNumber => $error) {
            printf('Error #%d' . PHP_EOL, $errorNumber);
            printf('  %s' . PHP_EOL, $error['message']);
            if (isset($error['lineNumber'])) {
                printf('  on line %d' . PHP_EOL, $error['lineNumber']);
            }
            if (isset($error['fileName'])) {
                printf('  in file %s' . PHP_EOL, $error['fileName']);
            }
            printf(PHP_EOL);
        }

        printf('%d errors occurred' . PHP_EOL, $this->countErrors());
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

    /**
     * Clears all former added errors
     * This is just for the tests and should not be called
     * within the normal work flow
     *
     * @return void
     */
    public function clearErrors()
    {
        $this->errors = array();
    }

    /**
     * This message is printed on the end if no error occurred
     *
     * @return void
     */
    public function printSuccessMessage()
    {
        printf('Successfully checked %d lines in %d files :)'. PHP_EOL, $this->lines, $this->files);
    }

    /**
     * Set number of files for success message
     *
     * @param int $files
     * @return void
     */
    public function setFiles($files)
    {
        $this->files = $files;
    }

    /**
     * Adds lines to print them in success message
     *
     * @param int $lines
     * @return void
     */
    public function addLines($lines)
    {
        $this->lines += $lines;
    }
}
