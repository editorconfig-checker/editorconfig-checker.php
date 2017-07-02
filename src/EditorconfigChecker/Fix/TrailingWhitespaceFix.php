<?php

namespace EditorconfigChecker\Fix;

use EditorconfigChecker\Utilities\Utilities;

class TrailingWhitespaceFix
{
    /**
     * Trims trailing whitespace from line
     *
     * @param string $filename
     * @param int $lineNumber
     * @param string $eolChar
     * @return boolean
     */
    public static function trim($filename, $lineNumber, $eolChar)
    {
        if (Utilities::backupFile($filename) && $eolChar) {
            $lines = file($filename);
            $lines[$lineNumber] = rtrim($lines[$lineNumber]) . $eolChar;

            $filepointer = fopen($filename, 'w');
            fwrite($filepointer, implode('', $lines));
            fclose($filepointer);

            return true;
        }

        return false;
    }
}
