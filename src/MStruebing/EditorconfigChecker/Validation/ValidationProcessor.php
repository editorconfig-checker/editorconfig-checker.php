<?php

namespace MStruebing\EditorconfigChecker\Validation;

use MStruebing\EditorconfigChecker\Editorconfig\Editorconfig;

class ValidationProcessor
{
    public function validate($file)
    {
        return true;
    }

    /**
     * Loop over files and get the editorconfig rules for this file
     * and invokes the acutal validation
     *
     * @param array $editorconfig
     * @param array $files
     * @return void
     */
    public static function validateFiles($editorconfigPath, $files)
    {
        $editorconfig = new Editorconfig();
        /* because that should not happen on every loop cycle */
        $editorconfigRulesArray = $editorconfig->getRulesAsArray($editorconfigPath);

        foreach ($files as $file) {
            $rules = $editorconfig->getRulesForFiletype($editorconfigRulesArray, $file);
            ValidationProcessor::validateFile($rules, $file);
        }
    }

    /**
     * Proccesses all validations for a single file
     *
     * @param array $rules
     * @param string $file
     * @return void
     */
    public static function validateFile($rules, $file)
    {
        $content = file($file);
        $lastIndentSize = null;

        foreach ($content as $lineNumber => $line) {
            $lastIndentSize = IndentationValidator::validate($rules, $line, $lineNumber, $lastIndentSize, $file);
            TrailingWhitespaceValidator::validate($rules, $line, $lineNumber, $file);
        }

        /* to prevent checking of empty files */
        if (isset($lineNumber)) {
            FinalNewlineValidator::validate($rules, $file, $content);
            LineEndingValidator::validate($rules, $file, file_get_contents($file), $lineNumber);
        }
    }
}
