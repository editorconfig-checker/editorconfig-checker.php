<?php

namespace EditorconfigChecker\Fix;

use EditorconfigChecker\Utilities\Utilities;

class FinalNewlineFix
{
    /**
     * Insert a final newline at the end of the file
     *
     * @param string $filename
     * @param string $eolChar
     * @return boolean
     */
    public function insert(string $filename, string $eolChar) : bool
    {
        $utilities = new Utilities();

        if ($utilities->backupFile($filename)) {
            return file_put_contents($filename, $eolChar, FILE_APPEND);
        }

        return false;
    }
}
