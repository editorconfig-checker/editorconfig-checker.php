<?php

use PHPUnit\Framework\TestCase;
use EditorconfigChecker\Utilities\Utilities;

final class UtilitiesTest extends TestCase
{
    public function testGetEndOfLineChar()
    {
        $utilities = new Utilities();

        $rules = ['end_of_line' => 'lf'];
        $this->assertEquals($utilities->getEndOfLineChar($rules), "\n");

        $rules = ['end_of_line' => 'cr'];
        $this->assertEquals($utilities->getEndOfLineChar($rules), "\r");

        $rules = ['end_of_line' => 'crlf'];
        $this->assertEquals($utilities->getEndOfLineChar($rules), "\r\n");

        $this->assertEquals($utilities->getEndOfLineChar([]), '');

        $rules = ['end_of_line' => 'abc'];
        $this->assertEquals($utilities->getEndOfLineChar($rules), '');

    }

    public function testGetDefaultExcludes()
    {
        $arr = [
            'vendor',
            'node_modules',
            '\.DS_Store',
            '\.gif$',
            '\.png$',
            '\.bmp$',
            '\.jpg$',
            '\.svg$',
            '\.ico$',
            '\.lock$',
            '\.eot$',
            '\.woff$',
            '\.woff2$',
            '\.ttf$',
            '\.bak$',
            '\.bin$',
            '\.min\.js$',
            '\.min\.css$',
            '\.js\.map$',
            '\.css\.map$',
            '\.pdf$',
            '\.jpg$',
            '\.jpeg$',
            '\.zip$',
            '\.gz$',
            '\.7z$',
            '\.bz2$',
            '\.log$',
        ];

        $str = 'vendor|node_modules|\.DS_Store|\.gif$|\.png$|\.bmp$|\.jpg$|\.svg$|\.ico$|\.lock$|\.eot$|\.woff$|\.woff2$|\.ttf$|\.bak$|\.bin$|\.min\.js$|\.min\.css$|\.js\.map$|\.css\.map$|\.pdf$|\.jpg$|\.jpeg$|\.zip$|\.gz$|\.7z$|\.bz2$|\.log$';

        $utilities = new Utilities();

        $this->assertEquals($utilities->getDefaultExcludesAsArray(), $arr);
        $this->assertEquals($utilities->getDefaultExcludesAsString(), $str);
    }
}
