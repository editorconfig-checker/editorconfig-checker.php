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
    public static function trim($filename)
    {
        if (is_file($filename)) {
            $lines = file($filename);

            foreach ($lines as $index => $line) {
                $lines[$index] = rtrim($line);
            }

            $fp = fopen($filename, 'w');
            fwrite($fp, implode(PHP_EOL, $lines));
            fclose($fp);

            return true;
        }

        return false;
    }
}
