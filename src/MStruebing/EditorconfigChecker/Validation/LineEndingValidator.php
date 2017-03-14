<?php

namespace MStruebing\EditorconfigChecker\Validation;

use MStruebing\EditorconfigChecker\Cli\Logger;

class LineEndingValidator
{
    /**
     * Checks for line endings if needed
     *
     * @param array $rules
     * @param string $file
     * @param string $content
     * @param int $lineNumbers
     * @return void
     *
     */
    public static function validate($rules, $file, $content, $lineNumbers)
    {
        if (isset($rules['end_of_line'])) {
            if ($rules['end_of_line'] === 'lf') {
                $eols = count(str_split(preg_replace("/[^\n]/", "", $content)));
            } elseif ($rules['end_of_line'] === 'cr') {
                $eols = count(str_split(preg_replace("/[^\r]/", "", $content)));
            } elseif ($rules['end_of_line'] === 'crlf') {
                $eols = count(str_split(preg_replace("/[^\r\n]/", "", $content)));
            }

            if (isset($rules['insert_final_newline']) && $rules['insert_final_newline']) {
                if ($eols !== $lineNumbers + 1) {
                    Logger::getInstance()->addError('Not all lines have the correct end of line character!', $file);
                    return false;
                }
            } else {
                if ($eols !== $lineNumbers) {
                    Logger::getInstance()->addError('Not all lines have the correct end of line character!', $file);
                    return false;
                }
            }
        }

        return true;
    }
}
