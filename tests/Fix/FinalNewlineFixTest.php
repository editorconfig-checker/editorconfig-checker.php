<?php

use PHPUnit\Framework\TestCase;
use EditorconfigChecker\Fix\FinalNewlineFix;

final class FinalNewlineFixTest extends TestCase
{
    /* https://stackoverflow.com/questions/18849927/verifying-that-two-files-are-identical-using-pure-php */
    private function compareFiles($file_a, $file_b)
    {
            die;
        if (filesize($file_a) == filesize($file_b))
        {
            $fp_a = fopen($file_a, 'rb');
            $fp_b = fopen($file_b, 'rb');

            while (($b = fread($fp_a, 4096)) !== false)
            {
                $b_b = fread($fp_b, 4096);
                if ($b !== $b_b)
                {
                    fclose($fp_a);
                    fclose($fp_b);
                    return false;
                }
            }

            fclose($fp_a);
            fclose($fp_b);

            return true;
        }

        return false;
    }

    public function testInsert()
    {
        $originalFile = './Build/TestFiles/Fix/InsertFinalNewline/original.php';
        $afterFixFile = './Build/TestFiles/Fix/InsertFinalNewline/afterFix.php';
        $eolChar = "\n";

        FinalNewlineFix::insert($originalFile, $eolChar);
        $this->assertEquals(sha1_file($originalFile), sha1_file($afterFixFile));
    }
}
