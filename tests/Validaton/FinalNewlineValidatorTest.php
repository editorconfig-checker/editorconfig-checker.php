<?php
use PHPUnit\Framework\TestCase;
use MStruebing\EditorconfigChecker\Validation\FinalNewlineValidator;

final class FinalNewlineValidatorTest extends TestCase
{
    public function testValidate()
    {
        $rules = ['insert_final_newline' => true];
        $file = 'src';

        $content = ['array', 'with', 'stuff'];
        $this->assertFalse(FinalNewlineValidator::validate($rules, $file, $content));

        $content = ['array', 'with', "stuff\n"];
        $this->assertTrue(FinalNewlineValidator::validate($rules, $file, $content));

        $content = ['array', "with\n", 'stuff'];
        $this->assertFalse(FinalNewlineValidator::validate($rules, $file, $content));

        $content = ['array', 'with', "\nstuff"];
        $this->assertFalse(FinalNewlineValidator::validate($rules, $file, $content));

        $content = [];
        $this->assertTrue(FinalNewlineValidator::validate($rules, $file, $content));

        $content = ["\n"];
        $this->assertTrue(FinalNewlineValidator::validate($rules, $file, $content));
    }
}
