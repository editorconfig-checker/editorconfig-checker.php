<?php

use PHPUnit\Framework\TestCase;
use EditorconfigChecker\Validation\IndentationValidator;
use EditorconfigChecker\Cli\Logger;

final class IndentationValidatorTest extends TestCase
{
    public function testValidateTabs()
    {
        $lineNumber = 1;
        $file = 'src';
        $rules = ['indent_style' => 'tab'];

        /* clear the logger errors before */
        Logger::getInstance()->clearErrors();

        $indentationValidator = new IndentationValidator();

        $line = "\tHi";
        $this->assertTrue($indentationValidator->validate($rules, $line, $lineNumber, $file));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = "\t\tHi";
        $this->assertTrue($indentationValidator->validate($rules, $line, $lineNumber, $file));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = "\tHi\t";
        $this->assertTrue($indentationValidator->validate($rules, $line, $lineNumber, $file));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = "Hi\t";
        $this->assertTrue($indentationValidator->validate($rules, $line, $lineNumber, $file));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = "\t *Hi";
        $this->assertTrue($indentationValidator->validate($rules, $line, $lineNumber, $file));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = " *Hi";
        $this->assertTrue($indentationValidator->validate($rules, $line, $lineNumber, $file));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = "\t  Hi";
        $this->assertFalse($indentationValidator->validate($rules, $line, $lineNumber, $file));
        $this->assertEquals(Logger::getInstance()->countErrors(), 1);

        $line = "\t Hi";
        $this->assertFalse($indentationValidator->validate($rules, $line, $lineNumber, $file));
        $this->assertEquals(Logger::getInstance()->countErrors(), 2);

        $line = "    Hi\t";
        $this->assertFalse($indentationValidator->validate($rules, $line, $lineNumber, $file));
        $this->assertEquals(Logger::getInstance()->countErrors(), 3);
    }

    public function testValidateSpaces()
    {
        $rules = ['indent_style' => 'space', 'indent_size' => 4];
        $lineNumber = 1;
        $file = 'src';

        /* clear the logger errors before */
        Logger::getInstance()->clearErrors();

        $indentationValidator = new IndentationValidator();

        $line = 'Hi';
        $this->assertTrue($indentationValidator->validate($rules, $line, $lineNumber, $file));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = '        Hi';
        $this->assertTrue($indentationValidator->validate($rules, $line, $lineNumber, $file));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = '    Hi    ';
        $this->assertTrue($indentationValidator->validate($rules, $line, $lineNumber, $file));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = 'Hi    ';
        $this->assertTrue($indentationValidator->validate($rules, $line, $lineNumber, $file));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = "\tHi    ";
        $this->assertFalse($indentationValidator->validate($rules, $line, $lineNumber, $file));
        $this->assertEquals(Logger::getInstance()->countErrors(), 1);

        $line = " * Hi";
        $this->assertTrue($indentationValidator->validate($rules, $line, $lineNumber, $file));
        $this->assertEquals(Logger::getInstance()->countErrors(), 1);

        $line = "     * Hi";
        $this->assertTrue($indentationValidator->validate($rules, $line, $lineNumber, $file));
        $this->assertEquals(Logger::getInstance()->countErrors(), 1);

        $line = "    \tHi    ";
        $this->assertFalse($indentationValidator->validate($rules, $line, $lineNumber, $file));
        $this->assertEquals(Logger::getInstance()->countErrors(), 2);

        $line = "     Hi    ";
        $this->assertFalse($indentationValidator->validate($rules, $line, $lineNumber, $file));
        $this->assertEquals(Logger::getInstance()->countErrors(), 3);
    }
}
