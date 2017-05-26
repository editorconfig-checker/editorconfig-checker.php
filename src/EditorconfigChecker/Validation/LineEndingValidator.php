<?php

namespace EditorconfigChecker\Validation;

use EditorconfigChecker\Cli\Logger;
use EditorconfigChecker\Fix\LineEndingFix;
use EditorconfigChecker\Utilities\Utilities;

class LineEndingValidator
{
    /**
     * Checks for line endings if needed
     *
     * @param array $rules
     * @param string $filename
     * @param string $content
     * @param int $lineNumbers
     * @param boolean $autoFix
     * @return void
     *
     */
    public static function validate($rules, $filename, $content, $lineNumbers, $autoFix)
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
                    Logger::getInstance()->addError('Not all lines have the correct end of line character!', $filename);

                    if ($autoFix && LineEndingFix::replace($filename, Utilities::getEndOfLineChar($rules))) {
                        Logger::getInstance()->errorFixed();
                    }
                    return false;
                }
            } else {
                if ($eols !== $lineNumbers) {
                    Logger::getInstance()->addError('Not all lines have the correct end of line character!', $filename);

                    if ($autoFix && LineEndingFix::replace($filename, Utilities::getEndOfLineChar($rules))) {
                        Logger::getInstance()->errorFixed();
                    }

                    return false;
                }
            }
        }

        return true;
    }
}
