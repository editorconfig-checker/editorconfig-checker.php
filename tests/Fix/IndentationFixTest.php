<?php

use PHPUnit\Framework\TestCase;
use EditorconfigChecker\Fix\IndentationFix;

final class IndendationFixTest extends TestCase
{
    public function testTabsToSpaces()
    {
        $originalFile = './Build/TestFiles/Fix/TabsToSpaces/original.php';
        $afterFixFile = './Build/TestFiles/Fix/TabsToSpaces/afterFix.php';

        IndentationFix::tabsToSpaces($originalFile, 4);
        IndentationFix::tabsToSpaces($originalFile, 4);
        IndentationFix::tabsToSpaces($originalFile, 4);
        $this->assertEquals(sha1_file($originalFile), sha1_file($afterFixFile));
        exec('git checkout -- ' . $originalFile);
    }
}
