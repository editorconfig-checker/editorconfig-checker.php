<?php

use PHPUnit\Framework\TestCase;
use EditorconfigChecker\Fix\LineEndingFix;

final class LineEndingFixTest extends TestCase
{
    public function testReplace()
    {
        $originalFile = './Build/TestFiles/Fix/LineEnding/original.php';
        $afterFixFile = './Build/TestFiles/Fix/LineEnding/afterFix.php';
        $eolChar = "\n";

        LineEndingFix::replace($originalFile, $eolChar);
        $this->assertEquals(sha1_file($originalFile), sha1_file($afterFixFile));
        exec('git checkout -- ' . $originalFile);
    }
}
