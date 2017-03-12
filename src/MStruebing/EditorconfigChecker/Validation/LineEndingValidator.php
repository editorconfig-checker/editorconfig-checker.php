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
     * @param int $lineNumbers
     * @return void
     *
     */
    public static function validate($rules, $file, $lineNumbers)
    {
        if (isset($rules['end_of_line'])) {
            $content = file_get_contents($file);

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
                }
            } else {
                if ($eols !== $lineNumbers) {
                    Logger::getInstance()->addError('Not all lines have the correct end of line character!', $file);
                }
            }
        }
    }
}
