<?php

use PHPUnit\Framework\TestCase;
use EditorconfigChecker\Utilities\Utilities;

final class UtilitiesTest extends TestCase
{
    public function testGetEndOfLineChar()
    {
        $rules = ['end_of_line' => 'lf'];
        $this->assertEquals(Utilities::getEndOfLineChar($rules), "\n");

        $rules = ['end_of_line' => 'cr'];
        $this->assertEquals(Utilities::getEndOfLineChar($rules), "\r");

        $rules = ['end_of_line' => 'crlf'];
        $this->assertEquals(Utilities::getEndOfLineChar($rules), "\r\n");

        $this->assertEquals(Utilities::getEndOfLineChar(null), null);

        $rules = ['end_of_line' => 'abc'];
        $this->assertEquals(Utilities::getEndOfLineChar($rules), null);

    }
}
