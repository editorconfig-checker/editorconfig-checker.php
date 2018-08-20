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
     */
    public function testRunWithH()
    {
        $cli = new Cli();
        $this->expectOutputString($this->printUsage());
        $this->assertEquals($cli->run(['h'=> true], []), null);
    }

    /**
     * @test
     */
    public function testRunWithHelp()
    {
        $cli = new Cli();
        $this->expectOutputString($this->printUsage());
        $this->assertEquals($cli->run(['help'=> true], []), null);
    }

    /**
     * This needs to be extracted into a variable
     */
    protected function printUsage()
    {
        printf('Usage:' . PHP_EOL);
        printf('editorconfig-checker [OPTIONS] <FILE>|<FILEGLOB>' . PHP_EOL);
        printf('available options:' . PHP_EOL);
        printf('-a, --auto-fix' . PHP_EOL);
        printf(
            "\twill automatically fix fixable issues(insert_final_newline, end_of_line, trim_trailing_whitespace)"
            . PHP_EOL
        );
        printf('-d, --dotfiles' . PHP_EOL);
        printf("\tuse this flag if you want exclude dotfiles" . PHP_EOL);
        printf('-e <PATTERN>, --exclude <PATTERN>' . PHP_EOL);
        printf("\tstring or regex to filter files which should not be checked" . PHP_EOL);
        printf('-i, --ignore-defaults'. PHP_EOL);
        printf("\twill ignore default excludes, see README for details" . PHP_EOL);
        printf('-h, --help'. PHP_EOL);
        printf("\twill print this help text" . PHP_EOL);
        printf('-l, --list-files'. PHP_EOL);
        printf("\twill print all files which are checked to stdout" . PHP_EOL);
    }

    /**
     * @test
     */
    public function testRunWithListFiles()
    {
        $cli = new Cli();
        $this->expectOutputString('./src/EditorconfigChecker/Utilities/Utilities.php'
            . PHP_EOL
            . 'total: 1 files'
            . PHP_EOL
        );
        $this->assertEquals($cli->run(['list-files'=> true], ['./src/EditorconfigChecker/Utilities/*.php']), null);
    }

    /**
     * @test
     */
    public function testRunWithL()
    {
        $cli = new Cli();
        $this->expectOutputString('./src/EditorconfigChecker/Utilities/Utilities.php'
            . PHP_EOL
            . 'total: 1 files'
            . PHP_EOL
        );
        $this->assertEquals($cli->run(['l'=> true], ['./src/EditorconfigChecker/Utilities/*.php']), null);
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
                '/vendor|node_modules|\.DS_Store|\.gif$|\.png$|\.bmp$|\.jpg$|\.svg$|\.ico$|\.lock$|\.eot$|\.woff$|\.woff2$|\.ttf$|\.bak$|\.bin$|\.min\.js$|\.min\.css$|\.js\.map$|\.css\.map$|\.pdf$|\.jpg$|\.jpeg$|\.zip$|\.gz$|\.7z$|\.bz2$|\.log$/'
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
                '/e1|vendor|node_modules|\.DS_Store|\.gif$|\.png$|\.bmp$|\.jpg$|\.svg$|\.ico$|\.lock$|\.eot$|\.woff$|\.woff2$|\.ttf$|\.bak$|\.bin$|\.min\.js$|\.min\.css$|\.js\.map$|\.css\.map$|\.pdf$|\.jpg$|\.jpeg$|\.zip$|\.gz$|\.7z$|\.bz2$|\.log$/'
            ),
            'OneExcludeWithoutI' => array(
                array('exclude' => 'exclude1'),
                '/exclude1|vendor|node_modules|\.DS_Store|\.gif$|\.png$|\.bmp$|\.jpg$|\.svg$|\.ico$|\.lock$|\.eot$|\.woff$|\.woff2$|\.ttf$|\.bak$|\.bin$|\.min\.js$|\.min\.css$|\.js\.map$|\.css\.map$|\.pdf$|\.jpg$|\.jpeg$|\.zip$|\.gz$|\.7z$|\.bz2$|\.log$/'
            ),
            'TwoEWithoutI' => array(
                array('e' => array('e1', 'e2')),
                '/e1|e2|vendor|node_modules|\.DS_Store|\.gif$|\.png$|\.bmp$|\.jpg$|\.svg$|\.ico$|\.lock$|\.eot$|\.woff$|\.woff2$|\.ttf$|\.bak$|\.bin$|\.min\.js$|\.min\.css$|\.js\.map$|\.css\.map$|\.pdf$|\.jpg$|\.jpeg$|\.zip$|\.gz$|\.7z$|\.bz2$|\.log$/'
            ),
            'TwoExcludeWithoutI' => array(
                array('exclude' => array('exclude1', 'exclude2')),
                '/exclude1|exclude2|vendor|node_modules|\.DS_Store|\.gif$|\.png$|\.bmp$|\.jpg$|\.svg$|\.ico$|\.lock$|\.eot$|\.woff$|\.woff2$|\.ttf$|\.bak$|\.bin$|\.min\.js$|\.min\.css$|\.js\.map$|\.css\.map$|\.pdf$|\.jpg$|\.jpeg$|\.zip$|\.gz$|\.7z$|\.bz2$|\.log$/'
            ),
            'OneExcludeAndOneEWithoutI' => array(
                array('exclude' => 'exclude1', 'e' => 'e1'),
                '/e1|exclude1|vendor|node_modules|\.DS_Store|\.gif$|\.png$|\.bmp$|\.jpg$|\.svg$|\.ico$|\.lock$|\.eot$|\.woff$|\.woff2$|\.ttf$|\.bak$|\.bin$|\.min\.js$|\.min\.css$|\.js\.map$|\.css\.map$|\.pdf$|\.jpg$|\.jpeg$|\.zip$|\.gz$|\.7z$|\.bz2$|\.log$/'
            ),
            'OneExcludeTwoEWithoutI' => array(
                array('exclude' => 'exclude1', 'e' => array('e1', 'e2')),
                '/e1|e2|exclude1|vendor|node_modules|\.DS_Store|\.gif$|\.png$|\.bmp$|\.jpg$|\.svg$|\.ico$|\.lock$|\.eot$|\.woff$|\.woff2$|\.ttf$|\.bak$|\.bin$|\.min\.js$|\.min\.css$|\.js\.map$|\.css\.map$|\.pdf$|\.jpg$|\.jpeg$|\.zip$|\.gz$|\.7z$|\.bz2$|\.log$/'
            ),
            'TwoExcludeTwoEWithoutI' => array(
                array('exclude' => array('exclude1', 'exclude2'), 'e' => array('e1', 'e2')),
                '/e1|e2|exclude1|exclude2|vendor|node_modules|\.DS_Store|\.gif$|\.png$|\.bmp$|\.jpg$|\.svg$|\.ico$|\.lock$|\.eot$|\.woff$|\.woff2$|\.ttf$|\.bak$|\.bin$|\.min\.js$|\.min\.css$|\.js\.map$|\.css\.map$|\.pdf$|\.jpg$|\.jpeg$|\.zip$|\.gz$|\.7z$|\.bz2$|\.log$/'
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
