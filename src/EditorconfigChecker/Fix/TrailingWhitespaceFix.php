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
    public static function trim(string $filename, int $lineNumber, string $eolChar) : bool
    {
        $utilities = new Utilities();
        if ($utilities->backupFile($filename)) {
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
