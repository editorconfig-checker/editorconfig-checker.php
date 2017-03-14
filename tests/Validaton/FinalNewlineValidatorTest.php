<?php

use PHPUnit\Framework\TestCase;
use EditorconfigChecker\Validation\FinalNewlineValidator;
use EditorconfigChecker\Cli\Logger;

final class FinalNewlineValidatorTest extends TestCase
{
    public function testValidate()
    {
        $rules = ['insert_final_newline' => true];
        $file = 'src';

        /* clear the logger errors before */
        Logger::getInstance()->clearErrors();

        $content = ['array', 'with', 'stuff'];
        $this->assertFalse(FinalNewlineValidator::validate($rules, $file, $content));
        $this->assertEquals(Logger::getInstance()->countErrors(), 1);

        $content = ['array', 'with', "stuff\n"];
        $this->assertTrue(FinalNewlineValidator::validate($rules, $file, $content));
        $this->assertEquals(Logger::getInstance()->countErrors(), 1);

        $content = ['array', "with\n", 'stuff'];
        $this->assertFalse(FinalNewlineValidator::validate($rules, $file, $content));
        $this->assertEquals(Logger::getInstance()->countErrors(), 2);

        $content = ['array', 'with', "\nstuff"];
        $this->assertFalse(FinalNewlineValidator::validate($rules, $file, $content));
        $this->assertEquals(Logger::getInstance()->countErrors(), 3);

        $content = [];
        $this->assertTrue(FinalNewlineValidator::validate($rules, $file, $content));
        $this->assertEquals(Logger::getInstance()->countErrors(), 3);

        $content = ["\n"];
        $this->assertTrue(FinalNewlineValidator::validate($rules, $file, $content));
        $this->assertEquals(Logger::getInstance()->countErrors(), 3);
    }
}
