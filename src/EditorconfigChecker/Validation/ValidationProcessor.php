<?php
// editorconfig-disable-file

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
    public function validateFiles(array $fileNames, bool $autoFix)
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
    public function validateFile(array $rules, string $fileName, bool $autoFix)
    {
        $content = file($fileName);

        $indentation = new IndentationValidator();
        $trailingWhitespace = new TrailingWhitespaceValidator();

        foreach ($content as $lineNumber => $line) {
            if ($this->isLineDisabled($line)) {
                continue;
            }
            $indentation->validate($rules, $line, $lineNumber, $fileName, $autoFix);
            $trailingWhitespace->validate($rules, $line, $lineNumber, $fileName, $autoFix);
        }

        /* to prevent checking of empty files */
        if (isset($lineNumber)) {
            $finalNewline = new FinalNewlineValidator();
            $lineEnding = new LineEndingValidator();

            $finalNewline->validate($rules, $fileName, $content, $autoFix);
            $lineEnding->validate($rules, $fileName, file_get_contents($fileName), $lineNumber, $autoFix);

            Logger::getInstance()->addLines($lineNumber);
        }
    }

    /**
     * Checks wether a line is disabled or not
     *
     * @param string $line the line to check
     *
     * @return bool wether the line contains editorconfig-disable-line or not
     */
    protected function isLineDisabled(string $line)
    {
        $isDisabled = preg_match('/editorconfig-disable-line/', $line);
        return $isDisabled > 0;
    }
}
