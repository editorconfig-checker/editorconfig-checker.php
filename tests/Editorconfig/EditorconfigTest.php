<?php

use PHPUnit\Framework\TestCase;
use EditorconfigChecker\Editorconfig\Editorconfig;

final class EditorconfigTest extends TestCase
{
    public function getRulesArray($fileName)
    {
        $rootDir = getcwd();
        $editorconfigPath = $rootDir . '/' . $fileName;

        $editorconfig = new Editorconfig();
        $rules = $editorconfig->getRulesAsArray($editorconfigPath);
        return $rules;
    }

    public function testGetRulesForFile()
    {
        $expectedRules = [
            'end_of_line' => 'lf',
            'insert_final_newline' => 1,
            'charset' => 'utf-8',
            'trim_trailing_whitespace' => '',
            'indent_style' => 'space',
            'indent_size' => 4,
            'root' => 1
        ];

        $editorconfig = new Editorconfig();
        $rules = $editorconfig->getRulesForFile('./Readme.md');
        $this->assertEquals($expectedRules, $rules);
    }
}
