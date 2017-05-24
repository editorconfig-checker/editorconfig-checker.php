<?php

namespace EditorconfigChecker\Fix;

class TrailingWhitespaceFix
{
    /**
     * Insert a final newline at the end of the file
     *
     * @param string $filename
     * @return boolean
     */
    public static function trim($filename, $lineNumber, $eolChar)
    {
        if (is_file($filename)) {
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
