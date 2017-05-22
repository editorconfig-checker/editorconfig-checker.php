<?php

namespace EditorconfigChecker\Fix;

class FinalNewlineFix
{
    /**
     * Insert a final newline at the end of the file
     *
     * @param string $file
     * @return boolean
     */
    public static function insert($filename)
    {
        if (is_file($filename)) {
            return file_put_contents($filename, PHP_EOL, FILE_APPEND);
        }

        return false;
    }

    /**
     * Removes a final newline at the end of the file
     *
     * @param string $file
     * @return boolean
     */
    public static function remove($filename)
    {
        if (is_file($filename)) {
            $lines = file($filename);
            $last = sizeof($lines) - 1 ;
            unset($lines[$last]);

            $fp = fopen($filename, 'w');
            fwrite($fp, implode('', $lines));
            fclose($fp);
        }

        return false;
    }
}
