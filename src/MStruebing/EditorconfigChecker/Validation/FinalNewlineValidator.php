<?php

namespace MStruebing\EditorconfigChecker\Validation;

use MStruebing\EditorconfigChecker\Cli\Logger;

class FinalNewlineValidator
{
    /**
     * Checks a file for final newline if needed
     *
     * @param array $rules
     * @param string $file
     * @param array $content
     * @return boolean
     */
    public static function validate($rules, $file, $content)
    {
        if (isset($rules['insert_final_newline']) && $rules['insert_final_newline'] && count($content)) {
            $lastLine = $content[count($content) - 1];
            preg_match('/(.*\n\Z)/', $lastLine, $matches);

            if (!isset($matches[1])) {
                Logger::getInstance()->addError('Missing final newline', $file);
                return false;
            }
        }

        return true;
    }
}
