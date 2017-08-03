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
    public static function validateFiles(array $fileNames, bool $autoFix) : void
    {
        /* Maybe make this an option? */
        $rootDir = getcwd();
        $editorconfig = new Editorconfig();

        foreach ($fileNames as $fileName) {
            $rules = $editorconfig->getRulesForFile($fileName, $rootDir);
            ValidationProcessor::validateFile($rules, $fileName, $autoFix);
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
    public static function validateFile(array $rules, string $fileName, bool $autoFix) : void
    {
        $content = file($fileName);

        foreach ($content as $lineNumber => $line) {
            IndentationValidator::validate($rules, $line, $lineNumber, $fileName);
            TrailingWhitespaceValidator::validate($rules, $line, $lineNumber, $fileName, $autoFix);
        }

        /* to prevent checking of empty files */
        if (isset($lineNumber)) {
            FinalNewlineValidator::validate($rules, $fileName, $content, $autoFix);
            LineEndingValidator::validate($rules, $fileName, file_get_contents($fileName), $lineNumber, $autoFix);
            Logger::getInstance()->addLines($lineNumber);
        }
    }
}
