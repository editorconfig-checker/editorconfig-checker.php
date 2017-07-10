<?php

use PHPUnit\Framework\TestCase;
use EditorconfigChecker\Cli\Cli;

final class CliTest extends TestCase
{
    public function getFileNamesDataProvider()
    {
        $rootDir = getcwd() . '/Build/TestFiles/FileNames/';
        return array(
            'oneDirectoryWithExcludeWithoutDotfiles' => array(
                array($rootDir . '**/*'),
                false,
                '/ExcludedDirectory/',
                array(
                    $rootDir . 'FileInRoot.php',
                    $rootDir . 'IncludedDirectory/IncludedFile.php',
                    $rootDir . 'PrefixedDirectory/IncludedFileInPrefixed.php'
                )
            ),
            'oneDirectoryWithExcludeWithDotfiles' => array(
                array($rootDir . '**/*'),
                true,
                '/ExcludedDirectory/',
                array(
                    $rootDir . '.DotFileInRoot',
                    $rootDir . 'FileInRoot.php',
                    $rootDir . 'IncludedDirectory/.IncludedDotFile',
                    $rootDir . 'IncludedDirectory/IncludedFile.php',
                    $rootDir . 'PrefixedDirectory/IncludedFileInPrefixed.php'
                )
            ),
            'oneDirectoryWithoutExcludeWithDotfiles' => array(
                array($rootDir . '**/*'),
                true,
                false,
                array(
                    $rootDir . '.DotFileInRoot',
                    $rootDir . 'ExcludedDirectory/ExcludedFile.php',
                    $rootDir . 'FileInRoot.php',
                    $rootDir . 'IncludedDirectory/.IncludedDotFile',
                    $rootDir . 'IncludedDirectory/IncludedFile.php',
                    $rootDir . 'PrefixedDirectory/IncludedFileInPrefixed.php'
                )
            )
        );
    }

    /**
     * @test
     * @dataProvider getFileNamesDataProvider
     */
    public function testGetFileNames($fileGlobs, $dotfiles, $excludedPattern, $expectedFiles)
    {
        $cli = new Cli;

        $fileNames = self::callMethod($cli, 'getFileNames', array(
            $fileGlobs,
            $dotfiles,
            $excludedPattern
        ));

        $this->assertEquals(
            $fileNames,
            $expectedFiles
        );
    }

    public function getExcludedPatternFromOptionsDataProvider()
    {
        return array(
            'NoExcludeAndNoE' => array(
                array(),
                false
            ),
            'OneE' => array(
                array('e' => 'e1'),
                '/e1/'
            ),
            'OneExclude' => array(
                array('exclude' => 'exclude1'),
                '/exclude1/'
            ),
            'TwoE' => array(
                array('e' => array('e1', 'e2')),
                '/e1|e2/'
            ),
            'TwoExclude' => array(
                array('exclude' => array('exclude1', 'exclude2')),
                '/exclude1|exclude2/'
            ),
            'OneExcludeAndOneE' => array(
                array('exclude' => 'exclude1', 'e' => 'e1'),
                '/e1|exclude1/'
            ),
            'OneExcludeTwoE' => array(
                array('exclude' => 'exclude1', 'e' => array('e1', 'e2')),
                '/e1|e2|exclude1/'
            ),
            'TwoExcludeTwoE' => array(
                array('exclude' => array('exclude1', 'exclude2'), 'e' => array('e1', 'e2')),
                '/e1|e2|exclude1|exclude2/'
            )
        );
    }

    /**
     * @test
     * @dataProvider getExcludedPatternFromOptionsDataProvider
     */
    public function testGetExcludedPatternFromOptions($options, $expectedPattern)
    {
        $cli = new Cli;

        $pattern = self::callMethod($cli, 'getExcludedPatternFromOptions', array(
            $options
        ));

        $this->assertEquals(
            $pattern,
            $expectedPattern
        );
    }

    public static function callMethod($obj, $name, array $args) {
        $class = new \ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($obj, $args);
    }
}
