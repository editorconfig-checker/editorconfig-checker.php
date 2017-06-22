<?php

namespace EditorconfigChecker\Fix;

use EditorconfigChecker\Utilities\Utilities;

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
        if (Utilities::backupFile($filename) && $eolChar) {
            $content = file_get_contents($filename);
            $content = preg_replace('~\R~u', $eolChar, $content);
            file_put_contents($filename, $content);

            return true;
        }

        return false;
    }
}
