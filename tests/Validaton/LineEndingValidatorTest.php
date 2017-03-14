<?php

use PHPUnit\Framework\TestCase;
use MStruebing\EditorconfigChecker\Validation\LineEndingValidator;
use MStruebing\EditorconfigChecker\Cli\Logger;

final class LineEndingValidatorTest extends TestCase
{
    public function testValidateLf()
    {
        $rules = ['end_of_line' => 'lf'];
        $file = 'src';

        /* clear the logger errors before */
        Logger::getInstance()->clearErrors();

        $content = "hello\nworld\n";
        $this->assertTrue(LineEndingValidator::validate($rules, $file, $content, 2));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $content = "\nhello\nworld\n";
        $this->assertTrue(LineEndingValidator::validate($rules, $file, $content, 3));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $content = "hello\rworld\r";
        $this->assertFalse(LineEndingValidator::validate($rules, $file, $content, 2));
        $this->assertEquals(Logger::getInstance()->countErrors(), 1);

        $content = "hello\r\nworld\r\n";
        $this->assertFalse(LineEndingValidator::validate($rules, $file, $content, 2));
        $this->assertEquals(Logger::getInstance()->countErrors(), 2);

        $content = "hello\nworld";
        $this->assertFalse(LineEndingValidator::validate($rules, $file, $content, 2));
        $this->assertEquals(Logger::getInstance()->countErrors(), 3);
    }
}
