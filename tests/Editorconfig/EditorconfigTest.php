<?php

use PHPUnit\Framework\TestCase;
use EditorconfigChecker\Editorconfig\Editorconfig;

final class EditorconfigTest extends TestCase
{
    protected $expectedRules;

    protected function setUp()
    {
        $this->expectedRules = [
            'indent_style' => 'space',
            'indent_size' => 2,
            'insert_final_newline' => true,
            'root' => 1
        ];
    }

    public function testGetRulesForFile()
    {
        $editorconfig = new Editorconfig();

        // expect rules for wildcard file pattern
        $rootDir = getcwd();
        $rules = $editorconfig->getRulesForFile('./Build/TestFiles/Editorconfig/myFile.txt', $rootDir);
        $this->assertEquals($this->expectedRules, $rules);

        // expect same rules if starting point differs
        $rootDir = getcwd() . '/Build/TestFiles/Editorconfig';
        $rules = $editorconfig->getRulesForFile('./myFile.txt', $rootDir);
        $this->assertEquals($this->expectedRules, $rules);

        // expect same rules if filename starts with non leading »./«
        $rootDir = getcwd() . '/Build/TestFiles/Editorconfig';
        $rules = $editorconfig->getRulesForFile('myFile.txt', $rootDir);
        $this->assertEquals($this->expectedRules, $rules);

        // expect merged rules for PHP file (wildcard & PHP match)
        $this->expectedRules['indent_size'] = 4;
        $rootDir = getcwd() . '/Build/TestFiles/Editorconfig';
        $rules = $editorconfig->getRulesForFile('./Build/TestFiles/Editorconfig/myFile.php', $rootDir);
        $this->assertEquals($this->expectedRules, $rules);
    }

    public function testGetRecursiveRulesForFile()
    {
        $editorconfig = new Editorconfig();

        // expect same rules for PHP file in subfolder with JSON only editorconf
        // (editorconfig rules not applied recursively)
        $this->expectedRules['indent_size'] = 4;
        $rootDir = getcwd() . '/Build/TestFiles/Editorconfig';
        $rules = $editorconfig->getRulesForFile('./onlyJsonRules/myFile.php', $rootDir);
        $this->assertEquals($this->expectedRules, $rules);

        // expect merged rules for JSON file in subfolder with JSON only
        // editorconf (editorconfig rules applied recursively)
        $this->expectedRules['indent_size'] = 6;
        $rootDir = getcwd() . '/Build/TestFiles/Editorconfig';
        $rules = $editorconfig->getRulesForFile('./onlyJsonRules/myFile.json', $rootDir);
        $this->assertEquals($this->expectedRules, $rules);

        // expect merged rules for PHP file in subfolder with mixed editorconf
        // (editorconfig rules applied recursively)
        $this->expectedRules['indent_style'] = 'tab';
        $this->expectedRules['indent_size'] = 4;
        $this->expectedRules['trim_trailing_whitespace'] = true;
        $this->expectedRules['insert_final_newline'] = false;

        $rootDir = getcwd() . '/Build/TestFiles/Editorconfig';
        $rules = $editorconfig->getRulesForFile('./ComposedRules/myFile.php', $rootDir);
        $this->assertEquals($this->expectedRules, $rules);

        // expect rules not getting merged recursively if the subfolder is the
        // starting point
        $this->expectedRules = [
            'indent_style' => 'tab',
            'insert_final_newline' => false,
            'trim_trailing_whitespace' => true
        ];
        $rootDir = getcwd() . '/Build/TestFiles/Editorconfig/ComposedRules';
        $rules = $editorconfig->getRulesForFile('./myFile.php', $rootDir);
        $this->assertEquals($this->expectedRules, $rules);

        // expect rules not getting merged recursively if the subfolder contains
        // a »root« flag in the editorconfig
        $rootDir = getcwd() . '/Build/TestFiles/Editorconfig';
        $rules = $editorconfig->getRulesForFile('./RootFlagRules/myFile.php', $rootDir);
        $this->assertEquals($this->expectedRules, $rules);
    }
}
