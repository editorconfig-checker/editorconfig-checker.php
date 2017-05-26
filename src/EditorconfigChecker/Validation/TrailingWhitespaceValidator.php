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
    public static function validate($rules, $line, $lineNumber, $filename, $autoFix)
    {
        if (strlen($line) > 0 && isset($rules['trim_trailing_whitespace']) && $rules['trim_trailing_whitespace']) {
            preg_match('/^.*[\t ]+$/', $line, $matches);
            if (isset($matches[0])) {
                Logger::getInstance()->addError('Trailing whitespace', $filename, $lineNumber + 1);

                if ($autoFix &&
                    TrailingWhitespaceFix::trim($filename, $lineNumber, Utilities::getEndOfLineChar($rules))) {
                    Logger::getInstance()->errorFixed();
                }

                return false;
            }
        }

        return true;
    }
}
