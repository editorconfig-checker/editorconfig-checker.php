<?php

namespace EditorconfigChecker\Utilities;

class Utilities
{
    /**
     * returns the end of line character from editorconfig rules
     *
     * @param array $rules
     * @returns string
     */
    public static function getEndOfLineChar($rules)
    {
        if (isset($rules['end_of_line'])) {
            return
                ($rules['end_of_line'] == 'lf' ? "\n" :
                ($rules['end_of_line'] == 'cr' ? "\r" :
                ($rules['end_of_line'] == 'crlf' ? "\r\n" :
                null)));
        }

        return null;
    }
}
