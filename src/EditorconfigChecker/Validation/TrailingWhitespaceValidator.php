<?php

namespace EditorconfigChecker\Validation;

use EditorconfigChecker\Cli\Logger;
use EditorconfigChecker\Fix\TrailingWhitespaceFix;
use EditorconfigChecker\Utilities\Utilities;

class TrailingWhitespaceValidator
{
    /**
     * Checks a line for trailing whitespace if needed
     *
     * @param array $rules
     * @param string $line
     * @param int $lineNumber
     * @param string $filename
     * @param boolean $autoFix
     * @return boolean
     */
    public function validate(array $rules, string $line, int $lineNumber, string $filename, bool $autoFix) : bool
    {
        if (strlen($line) > 0 && isset($rules['trim_trailing_whitespace']) && $rules['trim_trailing_whitespace']) {
            preg_match('/^.*[\t ]+$/', $line, $matches);
            if (isset($matches[0])) {
                Logger::getInstance()->addError('Trailing whitespace', $filename, $lineNumber + 1);

                $utilities = new Utilities();
                $trailingWhitespaceFix = new TrailingWhitespaceFix();
                if ($autoFix &&
                    $trailingWhitespaceFix->trim($filename, $lineNumber, $utilities->getEndOfLineChar($rules))) {
                    Logger::getInstance()->errorFixed();
                }

                return false;
            }
        }

        return true;
    }
}
