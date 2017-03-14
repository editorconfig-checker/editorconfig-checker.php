<?php

use PHPUnit\Framework\TestCase;
use MStruebing\EditorconfigChecker\Cli\Logger;

final class LoggerTest extends TestCase
{
    public function testAddError()
    {
        /* clear the logger errors before */
        Logger::getInstance()->clearErrors();

        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $message = 'Message';
        $file = 'src';
        $lineNumber = 1;

        Logger::getInstance()->addError($message, $file, $lineNumber);
        $this->assertEquals(Logger::getInstance()->countErrors(), 1);

        Logger::getInstance()->addError($message, $file);
        $this->assertEquals(Logger::getInstance()->countErrors(), 2);

        Logger::getInstance()->addError($message);
        $this->assertEquals(Logger::getInstance()->countErrors(), 3);

        Logger::getInstance()->clearErrors();
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);
    }

    public function clearErrors()
    {
        Logger::getInstance()->clearErrors();
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $message = 'Message';
        $file = 'src';
        $lineNumber = 1;

        Logger::getInstance()->addError($message, $file, $lineNumber);
        $this->assertEquals(Logger::getInstance()->countErrors(), 1);

        Logger::getInstance()->clearErrors();
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);
    }

    public function testGetInstance()
    {
        $this->assertTrue(Logger::getInstance() instanceof Logger);
    }
}
