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
        $autoFix = false;

        /* clear the logger errors before */
        Logger::getInstance()->clearErrors();

        $indentationValidator = new IndentationValidator();

        $line = "\tHi";
        $this->assertTrue($indentationValidator->validate($rules, $line, $lineNumber, $file, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = "\t\tHi";
        $this->assertTrue($indentationValidator->validate($rules, $line, $lineNumber, $file, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = "\tHi\t";
        $this->assertTrue($indentationValidator->validate($rules, $line, $lineNumber, $file, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = "Hi\t";
        $this->assertTrue($indentationValidator->validate($rules, $line, $lineNumber, $file, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = "\t *Hi";
        $this->assertTrue($indentationValidator->validate($rules, $line, $lineNumber, $file, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = " *Hi";
        $this->assertTrue($indentationValidator->validate($rules, $line, $lineNumber, $file, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = "\t  Hi";
        $this->assertFalse($indentationValidator->validate($rules, $line, $lineNumber, $file, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 1);

        $line = "\t Hi";
        $this->assertFalse($indentationValidator->validate($rules, $line, $lineNumber, $file, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 2);

        $line = "    Hi\t";
        $this->assertFalse($indentationValidator->validate($rules, $line, $lineNumber, $file, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 3);
    }

    public function testValidateSpaces()
    {
        $rules = ['indent_style' => 'space', 'indent_size' => 4];
        $lineNumber = 1;
        $file = 'src';
        $autoFix = false;

        /* clear the logger errors before */
        Logger::getInstance()->clearErrors();

        $indentationValidator = new IndentationValidator();

        $line = 'Hi';
        $this->assertTrue($indentationValidator->validate($rules, $line, $lineNumber, $file, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = '        Hi';
        $this->assertTrue($indentationValidator->validate($rules, $line, $lineNumber, $file, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = '    Hi    ';
        $this->assertTrue($indentationValidator->validate($rules, $line, $lineNumber, $file, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = 'Hi    ';
        $this->assertTrue($indentationValidator->validate($rules, $line, $lineNumber, $file, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = "\tHi    ";
        $this->assertFalse($indentationValidator->validate($rules, $line, $lineNumber, $file, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 1);

        $line = " * Hi";
        $this->assertTrue($indentationValidator->validate($rules, $line, $lineNumber, $file, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 1);

        $line = "     * Hi";
        $this->assertTrue($indentationValidator->validate($rules, $line, $lineNumber, $file, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 1);

        $line = "    \tHi    ";
        $this->assertFalse($indentationValidator->validate($rules, $line, $lineNumber, $file, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 2);

        $line = "     Hi    ";
        $this->assertFalse($indentationValidator->validate($rules, $line, $lineNumber, $file, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 3);
    }

    public function testValidateSpacesDefensiveRules()
    {
        $rules = ['indent_style' => 'space', 'indent_size' => 0];
        $lineNumber = 1;
        $file = 'src';
        $autoFix = false;

        /* clear the logger errors before */
        Logger::getInstance()->clearErrors();

        $indentationValidator = new IndentationValidator();

        // Should return true for indent_size of zero
        $line = '  Hi';
        $this->assertTrue($indentationValidator->validate($rules, $line, $lineNumber, $file, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);


        $rules = ['indent_style' => 'space'];

        $indentationValidator = new IndentationValidator();

        // Should return true if no indent_size is given
        $line = '  Hi';
        $this->assertTrue($indentationValidator->validate($rules, $line, $lineNumber, $file, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);
    }
}
