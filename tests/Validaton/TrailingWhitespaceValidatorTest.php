<?php

use PHPUnit\Framework\TestCase;
use EditorconfigChecker\Validation\TrailingWhitespaceValidator;
use EditorconfigChecker\Cli\Logger;

final class TrailingWhitespaceValidatorTest extends TestCase
{
    public function testValidate()
    {
        $rules = ['trim_trailing_whitespace' => true];
        $lineNumber = 1;
        $file = 'src';
        $autoFix = false;

        /* clear the logger errors before */
        Logger::getInstance()->clearErrors();

        $trailingWhitespaceValidator = new TrailingWhitespaceValidator();

        $line = 'heyho';
        $this->assertTrue($trailingWhitespaceValidator->validate($rules, $line, $lineNumber, $file, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = '';
        $this->assertTrue($trailingWhitespaceValidator->validate($rules, $line, $lineNumber, $file, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = ' heyho';
        $this->assertTrue($trailingWhitespaceValidator->validate($rules, $line, $lineNumber, $file, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = "\theyho";
        $this->assertTrue($trailingWhitespaceValidator->validate($rules, $line, $lineNumber, $file, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = 'heyho ';
        $this->assertFalse($trailingWhitespaceValidator->validate($rules, $line, $lineNumber, $file, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 1);

        $line = "heyho\t";
        $this->assertFalse($trailingWhitespaceValidator->validate($rules, $line, $lineNumber, $file, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 2);

        $line = ' ';
        $this->assertFalse($trailingWhitespaceValidator->validate($rules, $line, $lineNumber, $file, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 3);

        /* trim_trailing_whitespace have to be true explicitly */
        $line = 'ww ';
        $rules = ['trim_trailing_whitespace'];
        $this->assertTrue($trailingWhitespaceValidator->validate($rules, $line, $lineNumber, $file, $autoFix));
        $this->assertEquals(Logger::getInstance()->countErrors(), 3);
    }
}
