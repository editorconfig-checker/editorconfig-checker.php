<?php

use PHPUnit\Framework\TestCase;
use EditorconfigChecker\Fix\TrailingWhitespaceFix;

final class TrailingWhitespaceFixTest extends TestCase
{
    public function testInsert()
    {
        $originalFile = './Build/TestFiles/Fix/TrimTrailingWhitespace/original.php';
        $afterFixFile = './Build/TestFiles/Fix/TrimTrailingWhitespace/afterFix.php';
        $eolChar = "\n";

        TrailingWhitespaceFix::trim($originalFile, 0, $eolChar);
        TrailingWhitespaceFix::trim($originalFile, 1, $eolChar);
        TrailingWhitespaceFix::trim($originalFile, 2, $eolChar);
        $this->assertEquals(sha1_file($originalFile), sha1_file($afterFixFile));
        exec('git checkout -- ' . $originalFile);
    }
}
