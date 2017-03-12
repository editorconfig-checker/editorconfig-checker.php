<?php
use PHPUnit\Framework\TestCase;
use MStruebing\EditorconfigChecker\Validation\TrailingWhitespaceValidator;

final class TrailingWhitespaceValidatorTest extends TestCase
{
    public function testValidate()
    {
        $rules = ['trim_trailing_whitespace' => true];
        $lineNumber = 1;
        $file = 'src';

        $line = 'heyho';
        $this->assertTrue(TrailingWhitespaceValidator::validate($rules, $line, $lineNumber, $file));

        $line = '';
        $this->assertTrue(TrailingWhitespaceValidator::validate($rules, $line, $lineNumber, $file));

        $line = 'heyho ';
        $this->assertFalse(TrailingWhitespaceValidator::validate($rules, $line, $lineNumber, $file));

        $line = ' ';
        $this->assertFalse(TrailingWhitespaceValidator::validate($rules, $line, $lineNumber, $file));

        /* trim_trailing_whitespace have to be true explicitly */
        $line = 'ww ';
        $rules = ['trim_trailing_whitespace'];
        $this->assertTrue(TrailingWhitespaceValidator::validate($rules, $line, $lineNumber, $file));
    }
}
