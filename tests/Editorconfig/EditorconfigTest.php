<?php

use PHPUnit\Framework\TestCase;
use EditorconfigChecker\Editorconfig\Editorconfig;

final class EditorconfigTest extends TestCase
{
    public function testGetRulesForFile()
    {
        $expectedRules = [
            'indent_style' => 'space',
            'indent_size' => 4,
            'root' => 1
        ];

        $editorconfig = new Editorconfig();

        $rootDir = getcwd();
        $rules = $editorconfig->getRulesForFile('./Build/TestFiles/Editorconfig/myFile.php', $rootDir);
        $this->assertEquals($expectedRules, $rules);

        $rootDir = getcwd() . '/Build/TestFiles/Editorconfig';
        $rules = $editorconfig->getRulesForFile('./Build/TestFiles/Editorconfig/myFile.php', $rootDir);
        $this->assertEquals($expectedRules, $rules);

        $rootDir = getcwd() . '/Build/TestFiles/Editorconfig';
        $rules = $editorconfig->getRulesForFile('./Build/TestFiles/Editorconfig/onlyJsonRules/myFile.php', $rootDir);
        $this->assertEquals($expectedRules, $rules);

        $expectedRules['indent_size'] = 2;
        $rootDir = getcwd() . '/Build/TestFiles/Editorconfig';
        $rules = $editorconfig->getRulesForFile('./Build/TestFiles/Editorconfig/onlyJsonRules/myFile.json', $rootDir);
        $this->assertEquals($expectedRules, $rules);


        $expectedRules = [
            'indent_style' => 'tab',
            'trim_trailing_whitespace' => 1,
            'insert_final_newline' => 1
        ];

        $rootDir = getcwd() . '/Build/TestFiles/Editorconfig';
        $rules = $editorconfig->getRulesForFile('./ComposedRules/myFile.php', $rootDir);
        $this->assertEquals($expectedRules, $rules);

        /* test if files with non leading ./ return the same result */
        $rules = $editorconfig->getRulesForFile('ComposedRules/myFile.php', $rootDir);
        $this->assertEquals($expectedRules, $rules);


        $expectedRules = [
            'root' => 1,
            'indent_style' => 'tab'
        ];

        $rules = $editorconfig->getRulesForFile('SubRoot/myFile.php', $rootDir);
        $this->assertEquals($expectedRules, $rules);

        $expectedRules = [
            'root' => 1
        ];

        $rules = $editorconfig->getRulesForFile('SubRoot/myFile.json', $rootDir);
        $this->assertEquals($expectedRules, $rules);
    }
}
