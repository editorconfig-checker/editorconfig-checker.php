<?php

use PHPUnit\Framework\TestCase;
use EditorconfigChecker\Fix\FinalNewlineFix;

final class FinalNewlineFixTest extends TestCase
{
    public function testInsert()
    {
        $originalFile = './Build/TestFiles/Fix/InsertFinalNewline/original.php';
        $afterFixFile = './Build/TestFiles/Fix/InsertFinalNewline/afterFix.php';
        $eolChar = "\n";

        $finalNewlineFix = new FinalNewlineFix();
        $finalNewlineFix->insert($originalFile, $eolChar);

        $this->assertEquals(sha1_file($originalFile), sha1_file($afterFixFile));
        exec('git checkout -- ' . $originalFile);
    }
}
