<?php

namespace EditorconfigChecker\Cli;

use EditorconfigChecker\Editorconfig\Editorconfig;
use EditorconfigChecker\Validation\ValidationProcessor;
use Webmozart\Glob\Glob;
use Webmozart\PathUtil\Path;

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
     * if dotfiles is true dotfiles will be added too otherwise
     * dotfiles will be ignored
     * if excludedPattern is provided the files will be filtered
     * for the excludedPattern
     *
     * @param array $fileGlobs
     * @param boolean $dotfiles
     * @param array $excludedPattern
     * @return array
     */
    protected function getFileNames($fileGlobs, $dotfiles, $excludedPattern)
    {
        $fileNames = array();
        foreach ($fileGlobs as $fileGlob) {
            $absoluteGlob = Path::makeAbsolute($fileGlob, getcwd());

            $globResult = array_filter(
                Glob::glob($absoluteGlob),
                function ($path) use ($dotfiles, $excludedPattern) {
                    // No directories, no excluded files, and potentially no dotfiles
                    return !is_dir($path) &&
                        (!$excludedPattern || !preg_match($excludedPattern, $path)) &&
                        ($dotfiles || strpos(basename($path), '.') !== 0);
                }
            );

            $fileNames = array_merge($fileNames, $globResult);
        }

        return array_unique($fileNames);
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
        if (isset($excludedPattern)) {
            if (is_array($excludedPattern)) {
                $pattern = '/' . implode('|', $excludedPattern) . '/';
            } else {
                $pattern = '/' . $excludedPattern . '/';
            }
        }

        return $pattern;
    }

    /**
     * Checks if a filename ends with /. or /..
     * because this are special unix files
     *
     * @param string $filename
     * @return boolean
     */
    protected function isSpecialDir($fileName)
    {
        return substr($fileName, -2) === '/.' || substr($fileName, -3) === '/..';
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
        printf("\tuse this flag if you want to also include dotfiles/dotdirectories" . PHP_EOL);
        printf('-e <PATTERN>, --exclude <PATTERN>' . PHP_EOL);
        printf("\tstring or regex to filter files which should not be checked" . PHP_EOL);
        printf('-h, --help'. PHP_EOL);
        printf("\twill print this help text" . PHP_EOL);
        printf('-l, --list-files'. PHP_EOL);
        printf("\twill print all files which are checked to stdout" . PHP_EOL);
    }
}
