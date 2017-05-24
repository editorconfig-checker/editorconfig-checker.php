<?php

namespace EditorconfigChecker\Validation;

use EditorconfigChecker\Cli\Logger;
use EditorconfigChecker\Fix\TrailingWhitespaceFix;

class TrailingWhitespaceValidator
{
    /**
     * Checks a line for trailing whitespace if needed
     *
     * @param array $rules
     * @param string $line
     * @param int $lineNumber
     * @param string $filename
     * @return boolean
     */
    public static function validate($rules, $line, $lineNumber, $filename)
    {
        if (strlen($line) > 0 && isset($rules['trim_trailing_whitespace']) && $rules['trim_trailing_whitespace']) {
            preg_match('/^.*[\t ]+$/', $line, $matches);
            if (isset($matches[0])) {
                Logger::getInstance()->addError('Trailing whitespace', $filename, $lineNumber + 1);
                $eolChar = $rules['end_of_line'] == 'lf' ? "\n" : ($rules['end_of_line'] == 'cr' ? "\r" : "\r\n");

                if (TrailingWhitespaceFix::trim($filename, $lineNumber, $eolChar)) {
                    Logger::getInstance()->errorFixed();
                }

                return false;
            }
        }

        return true;
    }
}
