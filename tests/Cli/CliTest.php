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
                array($rootDir . '*'),
                false,
                '/ExcludedDirectory/',
                array(
                    $rootDir . 'FileInRoot.php',
                    $rootDir . 'IncludedDirectory/IncludedFile.php',
                    $rootDir . 'PrefixedDirectory/IncludedFileInPrefixed.php'
                )
            ),
            'oneDirectoryWithExcludeWithDotfiles' => array(
                array($rootDir . '*'),
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
                array($rootDir . '*'),
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
        $cli = new Cli();

        $fileNames = self::callMethod($cli, 'getFileNames', array(
            $fileGlobs,
            $dotfiles,
            $excludedPattern
        ));

        $this->assertEquals(
            sort($fileNames),
            sort($expectedFiles)
        );
    }

    public function getExcludedPatternFromOptionsDataProvider()
    {
        return array(
            'NoExcludeAndNoEWithoutI' => array(
                array(),
                '/vendor|node_modules|\.DS_Store|\.gif$|\.png$|\.bmp$|\.jpg$|\.svg$|\.ico$|\.lock$|\.eot$|\.woff$|\.woff2$|\.ttf$|\.bak$|\.bin$|\.min.js$|\.min.css$|\.pdf$|\.jpeg$/'
            ),
            'OneEWithI' => array(
                array('e' => 'e1', 'i' => false),
                '/e1/'
            ),
            'OneExcludeWithI' => array(
                array('exclude' => 'exclude1', 'i' => false),
                '/exclude1/'
            ),
            'TwoEWithI' => array(
                array('e' => array('e1', 'e2'), 'i' => false),
                '/e1|e2/'
            ),
            'TwoExcludeWithI' => array(
                array('exclude' => array('exclude1', 'exclude2'), 'i' => false),
                '/exclude1|exclude2/'
            ),
            'OneExcludeAndOneEWithI' => array(
                array('exclude' => 'exclude1', 'e' => 'e1', 'i' => false),
                '/e1|exclude1/'
            ),
            'OneExcludeTwoEWithI' => array(
                array('exclude' => 'exclude1', 'e' => array('e1', 'e2'), 'i' => false),
                '/e1|e2|exclude1/'
            ),
            'TwoExcludeTwoEWithI' => array(
                array('exclude' => array('exclude1', 'exclude2'), 'e' => array('e1', 'e2'), 'i' => false),
                '/e1|e2|exclude1|exclude2/'
            ),
            'OneEWithoutI' => array(
                array('e' => 'e1'),
                '/e1|vendor|node_modules|\.DS_Store|\.gif$|\.png$|\.bmp$|\.jpg$|\.svg$|\.ico$|\.lock$|\.eot$|\.woff$|\.woff2$|\.ttf$|\.bak$|\.bin$|\.min.js$|\.min.css$|\.pdf$|\.jpeg$/'
            ),
            'OneExcludeWithoutI' => array(
                array('exclude' => 'exclude1'),
                '/exclude1|vendor|node_modules|\.DS_Store|\.gif$|\.png$|\.bmp$|\.jpg$|\.svg$|\.ico$|\.lock$|\.eot$|\.woff$|\.woff2$|\.ttf$|\.bak$|\.bin$|\.min.js$|\.min.css$|\.pdf$|\.jpeg$/'
            ),
            'TwoEWithoutI' => array(
                array('e' => array('e1', 'e2')),
                '/e1|e2|vendor|node_modules|\.DS_Store|\.gif$|\.png$|\.bmp$|\.jpg$|\.svg$|\.ico$|\.lock$|\.eot$|\.woff$|\.woff2$|\.ttf$|\.bak$|\.bin$|\.min.js$|\.min.css$|\.pdf$|\.jpeg$/'
            ),
            'TwoExcludeWithoutI' => array(
                array('exclude' => array('exclude1', 'exclude2')),
                '/exclude1|exclude2|vendor|node_modules|\.DS_Store|\.gif$|\.png$|\.bmp$|\.jpg$|\.svg$|\.ico$|\.lock$|\.eot$|\.woff$|\.woff2$|\.ttf$|\.bak$|\.bin$|\.min.js$|\.min.css$|\.pdf$|\.jpeg$/'
            ),
            'OneExcludeAndOneEWithoutI' => array(
                array('exclude' => 'exclude1', 'e' => 'e1'),
                '/e1|exclude1|vendor|node_modules|\.DS_Store|\.gif$|\.png$|\.bmp$|\.jpg$|\.svg$|\.ico$|\.lock$|\.eot$|\.woff$|\.woff2$|\.ttf$|\.bak$|\.bin$|\.min.js$|\.min.css$|\.pdf$|\.jpeg$/'
            ),
            'OneExcludeTwoEWithoutI' => array(
                array('exclude' => 'exclude1', 'e' => array('e1', 'e2')),
                '/e1|e2|exclude1|vendor|node_modules|\.DS_Store|\.gif$|\.png$|\.bmp$|\.jpg$|\.svg$|\.ico$|\.lock$|\.eot$|\.woff$|\.woff2$|\.ttf$|\.bak$|\.bin$|\.min.js$|\.min.css$|\.pdf$|\.jpeg$/'
            ),
            'TwoExcludeTwoEWithoutI' => array(
                array('exclude' => array('exclude1', 'exclude2'), 'e' => array('e1', 'e2')),
                '/e1|e2|exclude1|exclude2|vendor|node_modules|\.DS_Store|\.gif$|\.png$|\.bmp$|\.jpg$|\.svg$|\.ico$|\.lock$|\.eot$|\.woff$|\.woff2$|\.ttf$|\.bak$|\.bin$|\.min.js$|\.min.css$|\.pdf$|\.jpeg$/'
            )
        );
    }

    /**
     * @test
     * @dataProvider getExcludedPatternFromOptionsDataProvider
     */
    public function testGetExcludedPatternFromOptions($options, $expectedPattern)
    {
        $cli = new Cli();

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
