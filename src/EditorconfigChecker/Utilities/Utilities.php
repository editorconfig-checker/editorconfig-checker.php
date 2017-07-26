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

    /**
     * Saves a backup of the given file to /tmp
     *
     * @param string $filename
     * @return boolean
     */
    public static function backupFile($filename)
    {
        $tmpPath = '/tmp/editorconfig-checker.php/';
        if (is_file($filename) && (is_dir($tmpPath) || mkdir($tmpPath))) {
            return copy(
                $filename,
                $tmpPath . pathinfo($filename)['basename'] . '-' . time() . '-' . sha1_file($filename)
            );
        }

        return false;
    }

    /**
     * returns the default exclude pattern
     *
     * @return string
     */
    public function getDefaultExcludes($asArray = true)
    {
        $defaults = [
            'vendor',
            'node_modules',
            '\.gif$',
            '\.png$',
            '\.bmp$',
            '\.jpg$',
            '\.svg$',
            '\.ico$',
            '\.lock$',
            '\.eot$',
            '\.woff$',
            '\.woff2$',
            '\.ttf$',
            '\.bak$',
            '\.bin$',
            '\.min.js$',
            '\.min.css$'
        ];

        return ($asArray ? $defaults : implode('|', $defaults));
    }
}
