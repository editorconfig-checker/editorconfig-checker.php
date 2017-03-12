<?php

namespace MStruebing\EditorconfigChecker\Validation;

use MStruebing\EditorconfigChecker\Cli\Logger;

class TrailingWhitespaceValidator
{
    /**
     * Checks a line for trailing whitespace if needed
     *
     * @param array $rules
     * @param string $line
     * @param int $lineNumber
     * @return void
     */
    public static function validate($rules, $line, $lineNumber, $file)
    {
        if (strlen($line) > 0 && isset($rules['trim_trailing_whitespace']) && $rules['trim_trailing_whitespace']) {
            preg_match('/^.*[\t ]+$/', $line, $matches);
            if (isset($matches[0])) {
                Logger::getInstance()->addError('Trailing whitespace', $file, $lineNumber + 1);
            }
        }
    }
}
