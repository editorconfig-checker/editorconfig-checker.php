<?php

namespace EditorconfigChecker\Cli;

use EditorconfigChecker\Editorconfig\Editorconfig;
use EditorconfigChecker\Validation\ValidationProcessor;

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

        if ($usage) {
            $this->printUsage();
            return;
        }

        $rootDir = getcwd();
        $editorconfigPath = $rootDir . '/.editorconfig';

        if (!is_file($editorconfigPath)) {
            Logger::getInstance()->addError('No .editorconfig found');
            return;
        }

        isset($options['dots']) || isset($options['d']) ? $dots = true : $dots = false;
        $excludedPattern = $this->getExcludedPatternFromOptions($options);

        $fileNames = $this->getFileNames($fileGlobs, $dots, $excludedPattern);
        $fileCount = count($fileNames);

        if ($showFiles) {
            foreach ($fileNames as $fileName) {
                printf('%s' . PHP_EOL, $fileName);
            }
            printf('total: %d files' . PHP_EOL, $fileCount);
        }

        if ($fileCount > 0) {
            ValidationProcessor::validateFiles($editorconfigPath, $fileNames);
        }

        Logger::getInstance()->setFiles($fileCount);
    }

    /**
     * Returns an array of files matching the fileglobs
     * if dots is true dotfiles will be added too otherwise
     * dotfiles will be ignored
     * if excludedPattern is provided the files will be filtered
     * for the excludedPattern
     *
     * @param array $fileGlobs
     * @param boolean $dots
     * @param array $excludedPattern
     * @return array
     */
    protected function getFileNames($fileGlobs, $dots, $excludedPattern)
    {
        $fileNames = array();
        foreach ($fileGlobs as $fileGlob) {
            /* if the glob is only a file */
            /* add it to the file array an continue the loop */
            if (is_file($fileGlob)) {
                if (!in_array($fileGlob, $fileNames)) {
                    array_push($fileNames, $fileGlob);
                }

                continue;
            }

            $dirPattern = pathinfo($fileGlob, PATHINFO_DIRNAME);
            $fileExtension = pathinfo($fileGlob, PATHINFO_EXTENSION);

            if (is_dir($dirPattern)) {
                $objects = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dirPattern));
                foreach ($objects as $fileName => $object) {
                    /* . and .. */
                    if (!$this->isSpecialDir($fileName) &&
                        /* filter for dotfiles */
                        ($dots || strpos($fileName, './.') !== 0)) {
                        if ($fileExtension && $fileExtension === pathinfo($fileName, PATHINFO_EXTENSION)) {
                            /* if I not specify a file extension as argv I get files twice */
                            if (!in_array($fileName, $fileNames)) {
                                array_push($fileNames, $fileName);
                            }
                        } elseif (!strlen($fileExtension)) {
                            /* if I not specify a file extension as argv I get files twice */
                            if (!in_array($fileName, $fileNames)) {
                                array_push($fileNames, $fileName);
                            }
                        }
                    }
                }
            }
        }

        if ($excludedPattern) {
            return $this->filterFiles($fileNames, $excludedPattern);
        } else {
            return $fileNames;
        }
    }

    /**
     * Filter files for excluded paths
     *
     * @param array $files
     * @param array|string $excludedPattern
     * @return array
     */
    protected function filterFiles($fileNames, $excludedPattern)
    {
        $filteredFileNames = [];

        foreach ($fileNames as $fileName) {
            if (preg_match($excludedPattern, $fileName) != 1) {
                array_push($filteredFileNames, $fileName);
            }
        }

        return $filteredFileNames;
    }

    /**
     * Get the excluded pattern from the options
     *
     * @param array $options
     * @return array
     */
    protected function getExcludedPatternFromOptions($options)
    {
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
        } else {
            return false;
        }

        if (is_array($excludedPattern)) {
            $pattern = '/' . implode('|', $excludedPattern) . '/';
        } else {
            $pattern = '/' . $excludedPattern . '/';
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
        printf('-h, --help'. PHP_EOL);
        printf("\twill print this help text" . PHP_EOL);
        printf('-l, --list-files'. PHP_EOL);
        printf("\twill print all files which are checked to stdout" . PHP_EOL);
        printf('-d, --dots' . PHP_EOL);
        printf("\tuse this flag if you want to also include dotfiles/dotdirectories" . PHP_EOL);
        printf('-e <PATTERN>, --exclude <PATTERN>' . PHP_EOL);
        printf("\tstring or regex to filter files which should not be checked" . PHP_EOL);
    }
}
