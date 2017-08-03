<?php

namespace EditorconfigChecker\Fix;

use EditorconfigChecker\Utilities\Utilities;

class IndentationFix
{
    /**
     * Trims trailing whitespace from line
     *
     * @param string $filename
     * @param int $amount
     * @return boolean
     */
    public static function tabsToSpaces(string $filename, int $lineNumber, int $amount) : bool
    {
        $utilities = new Utilities();

        if ($utilities->backupFile($filename)) {
            $lines = file($filename);
            $lines[$lineNumber] = preg_replace('/\t/', str_repeat(' ', $amount), $lines[$lineNumber]);

            $filepointer = fopen($filename, 'w');
            fwrite($filepointer, implode('', $lines));
            fclose($filepointer);

            return true;
        }

        return false;
    }
}
