<?php

namespace EditorconfigChecker\Fix;

class FinalNewlineFix
{
    /**
     * Insert a final newline at the end of the file
     *
     * @param string $filename
     * @param string $eolChar
     * @return boolean
     */
    public static function insert($filename, $eolChar)
    {
        if (is_file($filename) && $eolChar) {
            return file_put_contents($filename, $eolChar, FILE_APPEND);
        }

        return false;
    }
}
