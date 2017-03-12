<?php
use PHPUnit\Framework\TestCase;
use MStruebing\EditorconfigChecker\Validation\TrailingWhitespaceValidator;

class TrailingWhitespaceValidatorTest extends TestCase
{
    public function testValidate()
    {
        $rules = ['trim_trailing_whitespace' => true];
        $line = 'heyho';
        $lineNumber = 1;
        $file = 'src';

        $this->assertTrue(TrailingWhitespaceValidator::validate($rules, $line, $lineNumber, $file));

        $line = 'heyho ';
        $this->assertFalse(TrailingWhitespaceValidator::validate($rules, $line, $lineNumber, $file));
    }
}
