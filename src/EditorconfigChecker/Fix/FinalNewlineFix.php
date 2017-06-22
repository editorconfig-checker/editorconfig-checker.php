<?php

namespace EditorconfigChecker\Fix;

use EditorconfigChecker\Utilities\Utilities;

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
        if (Utilities::backupFile($filename) && $eolChar) {
            return file_put_contents($filename, $eolChar, FILE_APPEND);
        }

        return false;
    }
}
