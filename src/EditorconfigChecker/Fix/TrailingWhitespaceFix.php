<?php

namespace EditorconfigChecker\Fix;

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
        if (is_file($filename) && $eolChar) {
            $lines = file($filename);
            $lines[$lineNumber] = rtrim($lines[$lineNumber]) . $eolChar;

            $fp = fopen($filename, 'w');
            fwrite($fp, implode('', $lines));
            fclose($fp);

            return true;
        }

        return false;
    }
}
