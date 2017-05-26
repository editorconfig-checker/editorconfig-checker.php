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
     * @param array $editorconfig
     * @param array $files
     * @param boolean $autoFix
     * @return void
     */
    public static function validateFiles($editorconfigPath, $files, $autoFix)
    {
        $editorconfig = new Editorconfig();
        /* because that should not happen on every loop cycle */
        $editorconfigRulesArray = $editorconfig->getRulesAsArray($editorconfigPath);

        foreach ($files as $file) {
            $rules = $editorconfig->getRulesForFile($editorconfigRulesArray, substr($file, 2));
            ValidationProcessor::validateFile($rules, $file, $autoFix);
        }
    }

    /**
     * Proccesses all validations for a single file
     *
     * @param array $rules
     * @param string $file
     * @return void
     */
    public static function validateFile($rules, $file, $autoFix)
    {
        $content = file($file);
        $lastIndentSize = null;

        foreach ($content as $lineNumber => $line) {
            $lastIndentSize = IndentationValidator::validate($rules, $line, $lineNumber, $lastIndentSize, $file);
            TrailingWhitespaceValidator::validate($rules, $line, $lineNumber, $file, $autoFix);
        }

        /* to prevent checking of empty files */
        if (isset($lineNumber)) {
            FinalNewlineValidator::validate($rules, $file, $content, $autoFix);
            LineEndingValidator::validate($rules, $file, file_get_contents($file), $lineNumber, $autoFix);
            Logger::getInstance()->addLines($lineNumber);
        }
    }
}
