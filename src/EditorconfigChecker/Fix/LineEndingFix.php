<?php

namespace EditorconfigChecker\Fix;

class LineEndingFix
{
    /**
     * Replaces end of line characters
     *
     * @param string $filename
     * @param string $eolChar
     * @return boolean
     */
    public static function replace($filename, $eolChar)
    {
        if (is_file($filename) && $eolChar) {
            $lines = file($filename);

            foreach ($lines as &$line) {
                $line = rtrim($line);
            }

            $fp = fopen($filename, 'w');
            fwrite($fp, implode($eolChar, $lines));
            fclose($fp);

            return true;
        }

        return false;
    }
}
