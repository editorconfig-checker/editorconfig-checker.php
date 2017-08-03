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
    public static function replace(string $filename, string $eolChar) : bool
    {
        $utilities = new Utilities();

        if ($utilities->backupFile($filename)) {
            $content = file_get_contents($filename);
            /* $content = preg_replace('~\R~u', $eolChar, $content); */
            $content = preg_replace('~(*BSR_ANYCRLF)\R~', $eolChar, $content);
            file_put_contents($filename, $content);

            return true;
        }

        return false;
    }
}
