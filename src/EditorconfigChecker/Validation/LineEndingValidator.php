<?php

namespace EditorconfigChecker\Validation;

use EditorconfigChecker\Cli\Logger;

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
                str_replace("\n", '', $content, $eolsLF);
                str_replace("\r\n", '', $content, $eolsCRLF);
                $eols = $eolsLF - $eolsCRLF;
            } elseif ($rules['end_of_line'] === 'cr') {
                str_replace("\r", '', $content, $eolsCR);
                str_replace("\r\n", '', $content, $eolsCRLF);
                $eols = $eolsCR - $eolsCRLF;
            } elseif ($rules['end_of_line'] === 'crlf') {
                str_replace("\r\n", '', $content, $eols);
            }

            if (isset($rules['insert_final_newline']) && $rules['insert_final_newline']) {
                if ($eols !== $lineNumbers + 1) {
                    Logger::getInstance()->addError('Not all lines have the correct end of line character!', $file);
                    return false;
                }
            } else {
                if ($eols !== $lineNumbers) {
                    var_dump($eols, $lineNumbers);
                    Logger::getInstance()->addError('Not all lines have the correct end of line character!', $file);
                    return false;
                }
            }
        }

        return true;
    }
}
