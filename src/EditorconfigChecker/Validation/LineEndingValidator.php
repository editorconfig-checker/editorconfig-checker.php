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
     * @param int $lineNumber
     * @param boolean $autoFix
     * @return void
     *
     */
    public function validate(
        array $rules,
        string $filename,
        string $content,
        int $lineNumber,
        bool $autoFix
    ) : bool {
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
                if ($eols !== $lineNumber + 1) {
                    Logger::getInstance()->addError('Not all lines have the correct end of line character!', $filename);

                    if ($autoFix && LineEndingFix::replace($filename, $utilities->getEndOfLineChar($rules))) {
                        Logger::getInstance()->errorFixed();
                    }
                    return false;
                }
            } else {
                if ($eols !== $lineNumber) {
                    Logger::getInstance()->addError('Not all lines have the correct end of line character!', $filename);

                    if ($autoFix && LineEndingFix::replace($filename, $utilities->getEndOfLineChar($rules))) {
                        Logger::getInstance()->errorFixed();
                    }

                    return false;
                }
            }
        }

        return true;
    }
}
