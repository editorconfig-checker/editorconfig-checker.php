<?php

use PHPUnit\Framework\TestCase;
use EditorconfigChecker\Validation\ValidationProcessor;
use EditorconfigChecker\Cli\Logger;

final class ValidationProcessorTest extends TestCase
{
    public function testValidateLF()
    {
        $rules = ['indent_style' => 'space', 'indent_size' => '4'];
        $file = './Build/TestFiles/Validation/TestFile.php';
        $autoFix = false;

        $validationProcessor = new ValidationProcessor();

        /* clear the logger errors before */
        Logger::getInstance()->clearErrors();

        $validationProcessor->validateFiles([$file], $autoFix);
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $validationProcessor->validateFile($rules, $file, $autoFix);
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);
    }
}
