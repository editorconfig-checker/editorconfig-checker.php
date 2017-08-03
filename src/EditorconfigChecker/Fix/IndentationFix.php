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
    public static function tabsToSpaces($filename, $amount)
    {
        if (Utilities::backupFile($filename) && $amount) {
            $content = file_get_contents($filename);
            $content = preg_replace('/\t/', str_repeat(' ', $amount), $content);
            file_put_contents($filename, $content);

            return true;
        }

        return false;
    }
}
