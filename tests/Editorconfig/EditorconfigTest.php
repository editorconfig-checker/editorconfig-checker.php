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
        $allRules = $this->getRulesArray('.editorconfig');

        $expectedRules = [
            'end_of_line' => 'lf',
            'insert_final_newline' => 1,
            'charset' => 'utf-8',
            'trim_trailing_whitespace' => '',
            'indent_style' => 'space',
            'indent_size' => 4
        ];

        $editorconfig = new Editorconfig();
        $rules = $editorconfig->getRulesForFile($allRules, 'Readme.md');
        $this->assertEquals($expectedRules, $rules);
    }

    public function testOverwriteOfRules()
    {
        $allRules = $this->getRulesArray('Build/TestFiles/.editorconfig');

        $expectedRules = [
            'end_of_line' => 'lf',
            'insert_final_newline' => 1,
            'charset' => 'utf-8',
            'trim_trailing_whitespace' => 1,
            'indent_style' => 'space',
            'indent_size' => 4
        ];

        $editorconfig = new Editorconfig();
        $rules = $editorconfig->getRulesForFile($allRules, 'Readme.md');
        $this->assertEquals($expectedRules, $rules);
    }
}
