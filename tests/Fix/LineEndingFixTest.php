<?php

use PHPUnit\Framework\TestCase;
use EditorconfigChecker\Fix\LineEndingFix;

final class LineEndingFixTest extends TestCase
{
    public function testReplaceFromCrToLf()
    {
        $originalFile = './Build/TestFiles/Fix/LineEnding/CrToLf/original.php';
        $afterFixFile = './Build/TestFiles/Fix/LineEnding/CrToLf/afterFix.php';
        $eolChar = "\n";

        $lineEndingFix = new LineEndingFix();

        $lineEndingFix->replace($originalFile, $eolChar);
        $this->assertEquals(sha1_file($originalFile), sha1_file($afterFixFile));
        exec('git checkout -- ' . $originalFile);
    }

    public function testReplaceFromLfToCr()
    {
        $originalFile = './Build/TestFiles/Fix/LineEnding/LfToCr/original.php';
        $afterFixFile = './Build/TestFiles/Fix/LineEnding/LfToCr/afterFix.php';
        $eolChar = "\r";

        $lineEndingFix = new LineEndingFix();

        $lineEndingFix->replace($originalFile, $eolChar);
        $this->assertEquals(sha1_file($originalFile), sha1_file($afterFixFile));
        exec('git checkout -- ' . $originalFile);
    }

    public function testReplaceFromCrLfToLf()
    {
        $originalFile = './Build/TestFiles/Fix/LineEnding/CrLfToLf/original.php';
        $afterFixFile = './Build/TestFiles/Fix/LineEnding/CrLfToLf/afterFix.php';
        $eolChar = "\n";

        $lineEndingFix = new LineEndingFix();

        $lineEndingFix->replace($originalFile, $eolChar);
        $this->assertEquals(sha1_file($originalFile), sha1_file($afterFixFile));
        exec('git checkout -- ' . $originalFile);
    }

    public function testReplaceFromLfToCrLf()
    {
        $originalFile = './Build/TestFiles/Fix/LineEnding/LfToCrLf/original.php';
        $afterFixFile = './Build/TestFiles/Fix/LineEnding/LfToCrLf/afterFix.php';
        $eolChar = "\r\n";

        $lineEndingFix = new LineEndingFix();

        $lineEndingFix->replace($originalFile, $eolChar);
        $this->assertEquals(sha1_file($originalFile), sha1_file($afterFixFile));
        exec('git checkout -- ' . $originalFile);
    }

    public function testReplaceFromCrToCrLf()
    {
        $originalFile = './Build/TestFiles/Fix/LineEnding/CrToCrLf/original.php';
        $afterFixFile = './Build/TestFiles/Fix/LineEnding/CrToCrLf/afterFix.php';
        $eolChar = "\r\n";

        $lineEndingFix = new LineEndingFix();

        $lineEndingFix->replace($originalFile, $eolChar);
        $this->assertEquals(sha1_file($originalFile), sha1_file($afterFixFile));
        exec('git checkout -- ' . $originalFile);
    }

    public function testReplaceFromCrLfToCr()
    {
        $originalFile = './Build/TestFiles/Fix/LineEnding/CrLfToCr/original.php';
        $afterFixFile = './Build/TestFiles/Fix/LineEnding/CrLfToCr/afterFix.php';
        $eolChar = "\r";

        $lineEndingFix = new LineEndingFix();

        $lineEndingFix->replace($originalFile, $eolChar);
        $this->assertEquals(sha1_file($originalFile), sha1_file($afterFixFile));
        exec('git checkout -- ' . $originalFile);
    }
}
