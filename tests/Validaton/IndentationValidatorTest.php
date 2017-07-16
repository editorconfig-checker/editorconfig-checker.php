<?php

use PHPUnit\Framework\TestCase;
use EditorconfigChecker\Validation\IndentationValidator;
use EditorconfigChecker\Cli\Logger;

final class IndentationValidatorTest extends TestCase
{
    public function testValidateTabs()
    {
        $rules = ['indent_style' => 'tab'];
        $lineNumber = 1;
        $file = 'src';

        /* clear the logger errors before */
        Logger::getInstance()->clearErrors();

        $line = "\tHi";
        $this->assertTrue(IndentationValidator::validate($rules, $line, $lineNumber, $file));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = "\t\tHi";
        $this->assertTrue(IndentationValidator::validate($rules, $line, $lineNumber, $file));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = "\tHi\t";
        $this->assertTrue(IndentationValidator::validate($rules, $line, $lineNumber, $file));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = "Hi\t";
        $this->assertTrue(IndentationValidator::validate($rules, $line, $lineNumber, $file));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = "\t *Hi";
        $this->assertTrue(IndentationValidator::validate($rules, $line, $lineNumber, $file));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = " *Hi";
        $this->assertTrue(IndentationValidator::validate($rules, $line, $lineNumber, $file));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = "\t  Hi";
        $this->assertFalse(IndentationValidator::validate($rules, $line, $lineNumber, $file));
        $this->assertEquals(Logger::getInstance()->countErrors(), 1);

        $line = "\t Hi";
        $this->assertFalse(IndentationValidator::validate($rules, $line, $lineNumber, $file));
        $this->assertEquals(Logger::getInstance()->countErrors(), 2);

        $line = "    Hi\t";
        $this->assertFalse(IndentationValidator::validate($rules, $line, $lineNumber, $file));
        $this->assertEquals(Logger::getInstance()->countErrors(), 3);
    }

    public function testValidateSpaces()
    {
        $rules = ['indent_style' => 'space', 'indent_size' => 4];
        $lineNumber = 1;
        $file = 'src';

        /* clear the logger errors before */
        Logger::getInstance()->clearErrors();

        $line = 'Hi';
        $this->assertTrue(IndentationValidator::validate($rules, $line, $lineNumber, $file));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = '        Hi';
        $this->assertEquals(IndentationValidator::validate($rules, $line, $lineNumber, $file), 8);
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = '    Hi    ';
        $this->assertEquals(IndentationValidator::validate($rules, $line, $lineNumber, $file), 4);
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = 'Hi    ';
        $this->assertTrue(IndentationValidator::validate($rules, $line, $lineNumber, $file));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = "\tHi    ";
        $this->assertEquals(IndentationValidator::validate($rules, $line, $lineNumber, $file), 0);
        $this->assertEquals(Logger::getInstance()->countErrors(), 1);

        $line = " * Hi";
        $this->assertEquals(IndentationValidator::validate($rules, $line, $lineNumber, $file), 1);
        $this->assertEquals(Logger::getInstance()->countErrors(), 1);

        $line = "     * Hi";
        $this->assertEquals(IndentationValidator::validate($rules, $line, $lineNumber, $file), 5);
        $this->assertEquals(Logger::getInstance()->countErrors(), 1);

        $line = "    \tHi    ";
        $this->assertFalse(IndentationValidator::validate($rules, $line, $lineNumber, $file));
        $this->assertEquals(Logger::getInstance()->countErrors(), 2);
    }
}
