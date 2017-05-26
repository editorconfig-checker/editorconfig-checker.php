<?php

use PHPUnit\Framework\TestCase;
use EditorconfigChecker\Validation\LineEndingValidator;
use EditorconfigChecker\Cli\Logger;

final class LineEndingValidatorTest extends TestCase
{
    public function testValidateLf()
    {
        $rules = ['end_of_line' => 'lf'];
        $file = 'src';
        $autoFix = false;

        /* clear the logger errors before */
        Logger::getInstance()->clearErrors();

        $content = "hello\nworld\n";
        $this->assertTrue(LineEndingValidator::validate($rules, $file, $content, 2, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $content = "\nhello\nworld\n";
        $this->assertTrue(LineEndingValidator::validate($rules, $file, $content, 3, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $content = "hello\rworld\r";
        $this->assertFalse(LineEndingValidator::validate($rules, $file, $content, 2, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 1);

        $content = "hello\r\nworld\r\n";
        $this->assertFalse(LineEndingValidator::validate($rules, $file, $content, 2, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 2);

        $content = "hello\nworld\r\n";
        $this->assertFalse(LineEndingValidator::validate($rules, $file, $content, 2, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 3);

        $content = "hello\nworld";
        $this->assertFalse(LineEndingValidator::validate($rules, $file, $content, 2, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 4);

        /* with final newline it should be one line more */
        $rules = ['end_of_line' => 'lf', 'insert_final_newline' => true];

        $content = "hello\nworld\n";
        $this->assertFalse(LineEndingValidator::validate($rules, $file, $content, 2, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 5);

        $content = "hello\nworld\n\n";
        $this->assertTrue(LineEndingValidator::validate($rules, $file, $content, 2, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 5);
    }

    public function testValidateCR()
    {
        $rules = ['end_of_line' => 'cr'];
        $file = 'src';
        $autoFix = false;

        /* clear the logger errors before */
        Logger::getInstance()->clearErrors();

        $content = "hello\rworld\r";
        $this->assertTrue(LineEndingValidator::validate($rules, $file, $content, 2, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $content = "\rhello\rworld\r";
        $this->assertTrue(LineEndingValidator::validate($rules, $file, $content, 3, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $content = "hello\nworld\n";
        $this->assertFalse(LineEndingValidator::validate($rules, $file, $content, 2, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 1);

        $content = "hello\r\nworld\r\n";
        $this->assertFalse(LineEndingValidator::validate($rules, $file, $content, 2, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 2);

        $content = "hello\r\nworld\r";
        $this->assertFalse(LineEndingValidator::validate($rules, $file, $content, 2, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 3);

        $content = "hello\nworld";
        $this->assertFalse(LineEndingValidator::validate($rules, $file, $content, 2, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 4);

        /* with final newline it should be one line more */
        $rules = ['end_of_line' => 'cr', 'insert_final_newline' => true];

        $content = "hello\rworld\r";
        $this->assertFalse(LineEndingValidator::validate($rules, $file, $content, 2, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 5);

        $content = "hello\rworld\r\r";
        $this->assertTrue(LineEndingValidator::validate($rules, $file, $content, 2, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 5);
    }

    public function testValidateCRLF()
    {
        $rules = ['end_of_line' => 'crlf'];
        $file = 'src';
        $autoFix = false;

        /* clear the logger errors before */
        Logger::getInstance()->clearErrors();

        $content = "hello\r\nworld\r\n";
        $this->assertTrue(LineEndingValidator::validate($rules, $file, $content, 2, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $content = "\r\nhello\r\nworld\r\n";
        $this->assertTrue(LineEndingValidator::validate($rules, $file, $content, 3, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $content = "hello\nworld\n";
        $this->assertFalse(LineEndingValidator::validate($rules, $file, $content, 2, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 1);

        $content = "hello\rworld\r";
        $this->assertFalse(LineEndingValidator::validate($rules, $file, $content, 2, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 2);

        $content = "hello\r\nworld\r";
        $this->assertFalse(LineEndingValidator::validate($rules, $file, $content, 2, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 3);

        $content = "hello\r\nworld";
        $this->assertFalse(LineEndingValidator::validate($rules, $file, $content, 2, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 4);

        /* with final newline it should be one line more */
        $rules = ['end_of_line' => 'crlf', 'insert_final_newline' => true];

        $content = "hello\r\nworld\r\n";
        $this->assertFalse(LineEndingValidator::validate($rules, $file, $content, 2, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 5);

        $content = "hello\r\nworld\r\n\r\n";
        $this->assertTrue(LineEndingValidator::validate($rules, $file, $content, 2, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 5);
    }
}
