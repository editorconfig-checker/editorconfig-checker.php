<?php

namespace EditorconfigChecker\Cli;

use EditorconfigChecker\Editorconfig\Editorconfig;
use EditorconfigChecker\Validation\ValidationProcessor;
use EditorconfigChecker\Utilities\Utilities;
use Symfony\Component\Finder\Finder;

class Cli
{
    /**
     * Entry point of this class to invoke all needed steps
     *
     * @param array $options
     * @param array $fileGlobs
     * @return void
     */
    public function run($options, $fileGlobs)
    {
        $usage = count($fileGlobs) === 0 || isset($options['h']) || isset($options['help']);
        $showFiles = isset($options['l']) || isset($options['list-files']);
        $autoFix = isset($options['a']) || isset($options['auto-fix']);

        if ($usage) {
            $this->printUsage();
            return;
        }

        isset($options['dotfiles']) || isset($options['d']) ? $dotfiles = true : $dotfiles = false;
        $excludedPattern = $this->getExcludedPatternFromOptions($options);

        $fileNames = $this->getFileNames($fileGlobs, $dotfiles, $excludedPattern);
        $fileCount = count($fileNames);

        if ($showFiles) {
            foreach ($fileNames as $fileName) {
                printf('%s' . PHP_EOL, $fileName);
            }
            printf('total: %d files' . PHP_EOL, $fileCount);
        }

        if ($fileCount > 0) {
            ValidationProcessor::validateFiles($fileNames, $autoFix);
        }

        Logger::getInstance()->setFiles($fileCount);
    }

    /**
     * Returns an array of files matching the fileglobs
     * if excludedPattern is provided the files will be filtered
     * for the excludedPattern
     *
     * @param array $fileGlobs
     * @param boolean $ignoreDotFiles
     * @param array $excludedPattern
     * @return array
     */
    protected function getFileNames($fileGlobs, $ignoreDotFiles, $excludedPattern)
    {
        $fileNames = array();
        $finder = new Finder();
        $finderCalled = false;

        $filter = function (\SplFileInfo $file) use ($excludedPattern) {
            return $excludedPattern ? preg_match($excludedPattern, $file) !== 1 : false;
        };

        if (count($fileGlobs)) {
            // prefilter fileGlobs due to perfomance issues if it is done after
            $prefilteredGlobs = $fileGlobs;
            if ($excludedPattern) {
                $prefilteredGlobs = array_filter($fileGlobs, function ($glob) use ($excludedPattern) {
                    // wtf? it finds this file:
                    // https://github.com/symfony/finder/tree/master/Tests/Fixtures/with%20space
                    if ($glob === 'space' || $glob === 'space/foo.txt') {
                        return false;
                    }

                    return preg_match($excludedPattern, $glob) !== 1;
                });
            }

            foreach ($prefilteredGlobs as $fileGlob) {
                if (is_file($fileGlob)
                    && !in_array($fileGlob, $fileNames)
                    && (!$excludedPattern || preg_match($excludedPattern, $fileGlob) !== 1)) {
                    array_push($fileNames, $fileGlob);
                    continue;
                }

                $pathinfo = pathinfo($fileGlob);
                $pathinfo['dirname'] !== '.' ? $dirname = $pathinfo['dirname'] : $dirname = getcwd();
                $finder->files()->filter($filter)->in($dirname)->ignoreDotFiles($ignoreDotFiles);
                $finderCalled = true;
            }
        } else {
            $finder->files()->filter($filter)->in(getcwd())->ignoreDotFiles($ignoreDotFiles);
            $finderCalled = true;
        }

        if ($finderCalled) {
            foreach ($finder as $file) {
                if (!in_array($file->getPathName(), $fileNames)) {
                    array_push($fileNames, $file->getPathName());
                }
            }
        }

        return $fileNames;
    }

    /**
     * Get the excluded pattern from the options
     *
     * @param array $options
     * @return array
     */
    protected function getExcludedPatternFromOptions($options)
    {
        $pattern = false;
        $ignoreDefaults = isset($options['i']) || isset($options['ignore-defaults']);

        if (isset($options['e']) && !isset($options['exclude'])) {
            $excludedPattern = $options['e'];
        } elseif (!isset($options['e']) && isset($options['exclude'])) {
            $excludedPattern = $options['exclude'];
        } elseif (isset($options['e']) && isset($options['exclude'])) {
            if (is_array($options['e']) && is_array($options['exclude'])) {
                $excludedPattern = array_merge($options['e'], $options['exclude']);
            } elseif (is_array($options['e']) && !is_array($options['exclude'])) {
                array_push($options['e'], $options['exclude']);
                $excludedPattern = $options['e'];
            } elseif (!is_array($options['e']) && is_array($options['exclude'])) {
                array_push($options['exclude'], $options['e']);
                $excludedPattern = $options['exclude'];
            } else {
                $excludedPattern = [$options['e'], $options['exclude']];
            }
        }

        $utilities = new Utilities();

        if (isset($excludedPattern)) {
            if (is_array($excludedPattern) && !$ignoreDefaults) {
                $pattern = '/' . implode('|', array_merge($excludedPattern, $utilities->getDefaultExcludes())) . '/';
            } elseif (!is_array($excludedPattern) && !$ignoreDefaults) {
                $pattern = '/' . $excludedPattern . '|' . $utilities->getDefaultExcludes(false) . '/';
            } elseif (is_array($excludedPattern)) {
                $pattern = '/' . implode('|', $excludedPattern) . '/';
            } else {
                $pattern = '/' . $excludedPattern . '/';
            }
        } else {
            $pattern = '/' . $utilities->getDefaultExcludes(false) . '/';
        }

        return $pattern;
    }

    /**
     * Prints the usage
     *
     * @return void
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
}
