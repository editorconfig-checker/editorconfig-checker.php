<?php

namespace EditorconfigChecker\Validation;

use EditorconfigChecker\Editorconfig\Editorconfig;
use EditorconfigChecker\Cli\Logger;

class ValidationProcessor
{
    /**
     * Loop over files and get the editorconfig rules for this file
     * and invokes the acutal validation
     *
     * @param array $fileNames
     * @param boolean $autoFix
     * @return void
     */
    public function validateFiles(array $fileNames, bool $autoFix) : void
    {
        /* Maybe make this an option? */
        $rootDir = getcwd();
        $editorconfig = new Editorconfig();

        foreach ($fileNames as $fileName) {
            $rules = $editorconfig->getRulesForFile($fileName, $rootDir);
            $this->validateFile($rules, $fileName, $autoFix);
        }
    }

    /**
     * Proccesses all validations for a single file
     *
     * @param array $rules
     * @param string $fileName
     * @param boolean $autoFix
     * @return void
     */
    public function validateFile(array $rules, string $fileName, bool $autoFix) : void
    {
        $content = file($fileName);

        $indentationValidator = new IndentationValidator();
        $trailingWhitespaceValidator = new TrailingWhitespaceValidator();

        foreach ($content as $lineNumber => $line) {
            $indentationValidator->validate($rules, $line, $lineNumber, $fileName);
            $trailingWhitespaceValidator->validate($rules, $line, $lineNumber, $fileName, $autoFix);
        }

        /* to prevent checking of empty files */
        if (isset($lineNumber)) {
            $finalNewlineValidator = new FinalNewlineValidator();
            $lineEndingValidator = new LineEndingValidator();

            $finalNewlineValidator->validate($rules, $fileName, $content, $autoFix);
            $lineEndingValidator->validate($rules, $fileName, file_get_contents($fileName), $lineNumber, $autoFix);

            Logger::getInstance()->addLines($lineNumber);
        }
    }
}
