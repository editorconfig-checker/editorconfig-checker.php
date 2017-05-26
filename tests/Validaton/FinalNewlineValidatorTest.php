<?php

use PHPUnit\Framework\TestCase;
use EditorconfigChecker\Validation\FinalNewlineValidator;
use EditorconfigChecker\Cli\Logger;

final class FinalNewlineValidatorTest extends TestCase
{
    public function testValidateLF()
    {
        $rules = ['insert_final_newline' => true, 'end_of_line' => 'lf'];
        $file = 'src';
        $autoFix = false;

        /* clear the logger errors before */
        Logger::getInstance()->clearErrors();

        $content = ['array', 'with', 'stuff'];
        $this->assertFalse(FinalNewlineValidator::validate($rules, $file, $content, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 1);

        $content = ['array', 'with', "stuff\n"];
        $this->assertTrue(FinalNewlineValidator::validate($rules, $file, $content, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 1);

        $content = ['array', "with\n", 'stuff'];
        $this->assertFalse(FinalNewlineValidator::validate($rules, $file, $content, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 2);

        $content = ['array', 'with', "\nstuff"];
        $this->assertFalse(FinalNewlineValidator::validate($rules, $file, $content, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 3);

        $content = [];
        $this->assertTrue(FinalNewlineValidator::validate($rules, $file, $content, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 3);

        $content = ["\n"];
        $this->assertTrue(FinalNewlineValidator::validate($rules, $file, $content, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 3);

        $content = ['array', 'with', "stuff\r"];
        $this->assertFalse(FinalNewlineValidator::validate($rules, $file, $content, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 4);

        $content = ['array', 'with', "stuff\r\n"];
        $this->assertFalse(FinalNewlineValidator::validate($rules, $file, $content, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 5);

        $rules = ['end_of_line' => 'lf'];

        $content = ['array', 'with', "stuff"];
        $this->assertTrue(FinalNewlineValidator::validate($rules, $file, $content, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 5);
    }

    public function testValidateCR()
    {
        $rules = ['insert_final_newline' => true, 'end_of_line' => 'cr'];
        $file = 'src';
        $autoFix = false;

        /* clear the logger errors before */
        Logger::getInstance()->clearErrors();

        $content = ['array', 'with', 'stuff'];
        $this->assertFalse(FinalNewlineValidator::validate($rules, $file, $content, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 1);

        $content = ['array', 'with', "stuff\r"];
        $this->assertTrue(FinalNewlineValidator::validate($rules, $file, $content, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 1);

        $content = ['array', "with\r", 'stuff'];
        $this->assertFalse(FinalNewlineValidator::validate($rules, $file, $content, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 2);

        $content = ['array', 'with', "\rstuff"];
        $this->assertFalse(FinalNewlineValidator::validate($rules, $file, $content, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 3);

        $content = [];
        $this->assertTrue(FinalNewlineValidator::validate($rules, $file, $content, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 3);

        $content = ["\r"];
        $this->assertTrue(FinalNewlineValidator::validate($rules, $file, $content, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 3);

        $content = ['array', 'with', "stuff\n"];
        $this->assertFalse(FinalNewlineValidator::validate($rules, $file, $content, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 4);

        $content = ['array', 'with', "stuff\r\n"];
        $this->assertFalse(FinalNewlineValidator::validate($rules, $file, $content, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 5);

        $rules = ['end_of_line' => 'lf'];

        $content = ['array', 'with', "stuff"];
        $this->assertTrue(FinalNewlineValidator::validate($rules, $file, $content, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 5);
    }

    public function testValidateCRLF()
    {
        $rules = ['insert_final_newline' => true, 'end_of_line' => 'crlf'];
        $file = 'src';
        $autoFix = false;

        /* clear the logger errors before */
        Logger::getInstance()->clearErrors();

        $content = ['array', 'with', 'stuff'];
        $this->assertFalse(FinalNewlineValidator::validate($rules, $file, $content, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 1);

        $content = ['array', 'with', "stuff\r\n"];
        $this->assertTrue(FinalNewlineValidator::validate($rules, $file, $content, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 1);

        $content = ['array', "with\r\n", 'stuff'];
        $this->assertFalse(FinalNewlineValidator::validate($rules, $file, $content, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 2);

        $content = ['array', 'with', "\r\nstuff"];
        $this->assertFalse(FinalNewlineValidator::validate($rules, $file, $content, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 3);

        $content = [];
        $this->assertTrue(FinalNewlineValidator::validate($rules, $file, $content, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 3);

        $content = ["\r\n"];
        $this->assertTrue(FinalNewlineValidator::validate($rules, $file, $content, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 3);

        $content = ['array', 'with', "stuff\n"];
        $this->assertFalse(FinalNewlineValidator::validate($rules, $file, $content, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 4);

        $content = ['array', 'with', "stuff\r"];
        $this->assertFalse(FinalNewlineValidator::validate($rules, $file, $content, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 5);

        $content = ['array', 'with', "stuff\n"];
        $this->assertFalse(FinalNewlineValidator::validate($rules, $file, $content, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 6);

        $rules = ['end_of_line' => 'lf'];

        $content = ['array', 'with', "stuff"];
        $this->assertTrue(FinalNewlineValidator::validate($rules, $file, $content, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 6);
    }

    public function testValidateWithNoLineEnding()
    {
        $rules = ['insert_final_newline' => true];
        $file = 'src';
        $autoFix = false;

        /* clear the logger errors before */
        Logger::getInstance()->clearErrors();

        $content = ['array', 'with', "stuff\r\n"];
        $this->assertTrue(FinalNewlineValidator::validate($rules, $file, $content, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $content = ['array', 'with', "stuff\n"];
        $this->assertTrue(FinalNewlineValidator::validate($rules, $file, $content, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $content = ['array', 'with', "stuff\r"];
        $this->assertTrue(FinalNewlineValidator::validate($rules, $file, $content, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $content = ['array', 'with', 'stuff'];
        $this->assertFalse(FinalNewlineValidator::validate($rules, $file, $content, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 1);
    }
}
