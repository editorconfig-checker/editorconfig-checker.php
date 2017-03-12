<?php

namespace MStruebing\EditorconfigChecker\Cli;

use MStruebing\EditorconfigChecker\Validation\Validator;
use MStruebing\EditorconfigChecker\Editorconfig\Editorconfig;

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
        count($fileGlobs) === 0 || isset($options['h']) || isset($options['help']) ? $usage = true : $usage = false;

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

        $files = $this->getFiles($fileGlobs, $dots, $excludedPattern);

        if (count($files) > 0) {
            $this->checkFiles($editorconfigPath, $files);
        }
    }

    /**
     * Loop over files and get the editorconfig rules for this file and invokes the check
     *
     * @param array $editorconfig
     * @param array $files
     * @return void
     */
    protected function checkFiles($editorconfigPath, $files)
    {
        $editorconfig = new Editorconfig();
        /* because that should not happen on every loop cycle */
        $editorconfigRulesArray = $editorconfig->getRulesAsArray($editorconfigPath);

        foreach ($files as $file) {
            $rules = $editorconfig->getRulesForFiletype($editorconfigRulesArray, $file);
            $this->processCheckForSingleFile($rules, $file);
        }
    }

    /**
     * Proccesses all checks for a single file
     *
     * @param array $rules
     * @param string $file
     * @return void
     */
    protected function processCheckForSingleFile($rules, $file)
    {
        $content = file($file);
        $lastIndentSize = null;

        foreach ($content as $lineNumber => $line) {
            $lastIndentSize = $this->checkForIndentation($rules, $line, $lineNumber, $lastIndentSize, $file);
            $this->checkForTrailingWhitespace($rules, $line, $lineNumber, $file);
        }

        /* to prevent checking of empty files */
        if (isset($lineNumber)) {
            $this->checkForFinalNewline($rules, $file, $content);
            $this->checkForLineEnding($rules, $file, $lineNumber);
        }
    }

    /**
     * Determines if the file is to check for spaces or tabs
     *
     * @param array $rules
     * @param string $line
     * @param int $lineNumber
     * @param int $lastIndentSize
     * @param string $file
     * @return int
     */
    protected function checkForIndentation($rules, $line, $lineNumber, $lastIndentSize, $file)
    {
        if (isset($rules['indent_style']) && $rules['indent_style'] === 'space') {
            $lastIndentSize = $this->checkForSpace($rules, $line, $lineNumber, $lastIndentSize, $file);
        } elseif (isset($rules['indent_style']) && $rules['indent_style'] === 'tab') {
            $lastIndentSize = $this->checkForTab($rules, $line, $lineNumber, $lastIndentSize, $file);
        } else {
            $lastIndentSize = 0;
        }

        return $lastIndentSize;
    }

    /**
     * Processes indentation check for spaces
     *
     * @param array $rules
     * @param string $line
     * @param int $lineNumber
     * @param int $lastIndentSize
     * @param string $file
     * @return void
     */
    protected function checkForSpace($rules, $line, $lineNumber, $lastIndentSize, $file)
    {
        preg_match('/^( +)/', $line, $matches);

        if (isset($matches[1])) {
            $indentSize = strlen($matches[1]);

            /* check if the indentation size could be a valid one */
            /* the * is for function comments */
            if ($indentSize % $rules['indent_size'] !== 0 && $line[$indentSize] !== '*') {
                Logger::getInstance()->addError('Not the right amount of spaces', $file, $lineNumber + 1);
            }

            /* because the following example would not work I have to check it this way */
            /* ... maybe it should not? */
            /* if (xyz) */
            /* { */
            /*     throw new Exception('hello */
            /*         world');  <--- this is the critial part */
            /* } */
            if (isset($lastIndentSize) && ($indentSize - $lastIndentSize) > $rules['indent_size']) {
                Logger::getInstance()->addError(
                    'Not the right relation of spaces between lines',
                    $file,
                    $lineNumber + 1
                );
            }

            $lastIndentSize = $indentSize;
        } else { /* if no matching leading spaces found check if tabs are there instead */
            preg_match('/^(\t+)/', $line, $matches);
            if (isset($matches[1])) {
                Logger::getInstance()->addError('Wrong indentation type', $file, $lineNumber + 1);
            }
        }

        if (!isset($indentSize)) {
            $indentSize = null;
        }

        return $indentSize;
    }

    /**
     * Processes indentation check for tabs
     *
     * @param array $rules
     * @param string $line
     * @param int $lineNumber
     * @param int $lastIndentSize
     * @param string $file
     * @return void
     */
    protected function checkForTab($rules, $line, $lineNumber, $lastIndentSize, $file)
    {
        preg_match('/^(\t+)/', $line, $matches);

        if (isset($matches[1])) {
            $indentSize = strlen($matches[1]);

            /* because the following example would not work I have to check it this way */
            /* ... maybe it should not? */
            /* if (xyz) */
            /* { */
            /*     throw new Exception('hello */
            /*         world');  <--- this is the critial part */
            /* } */
            if (isset($lastIndentSize) && ($indentSize - $lastIndentSize) > 1) {
                Logger::getInstance()->addError('Not the right relation of tabs between lines', $file, $lineNumber + 1);
            }

            $lastIndentSize = $indentSize;
        } else { /* if no matching leading tabs found check if spaces are there instead */
            preg_match('/^( +)/', $line, $matches);
            if (isset($matches[1])) {
                Logger::getInstance()->addError('Wrong indentation type', $file, $lineNumber + 1);
            }
        }

        if (!isset($indentSize)) {
            $indentSize = null;
        }

        return $indentSize;
    }

    /**
     * Checks a line for trailing whitespace if needed
     *
     * @param array $rules
     * @param string $line
     * @param int $lineNumber
     * @return void
     */
    protected function checkForTrailingWhitespace($rules, $line, $lineNumber, $file)
    {
        if (strlen($line) > 0 && isset($rules['trim_trailing_whitespace']) && $rules['trim_trailing_whitespace']) {
            preg_match('/^.*[\t ]+$/', $line, $matches);
            if (isset($matches[0])) {
                Logger::getInstance()->addError('Trailing whitespace', $file, $lineNumber + 1);
            }
        }
    }

    /**
     * Checks a file for final newline if needed
     *
     * @param array $rules
     * @param string $file
     * @param array $content
     * @return void
     */
    protected function checkForFinalNewline($rules, $file, $content)
    {
        if (isset($rules['insert_final_newline']) && $rules['insert_final_newline']) {
            $lastLine = $content[count($content) - 1];
            preg_match('/(.*\n\Z)/', $lastLine, $matches);

            if (!isset($matches[1])) {
                Logger::getInstance()->addError('Missing final newline', $file);
            }
        }
    }

    /**
     * Checks for line endings if needed
     *
     * @param array $rules
     * @param string $file
     * @param int $lineNumbers
     * @return void
     *
     */
    protected function checkForLineEnding($rules, $file, $lineNumbers)
    {
        if (isset($rules['end_of_line'])) {
            $content = file_get_contents($file);

            if ($rules['end_of_line'] === 'lf') {
                $eols = count(str_split(preg_replace("/[^\n]/", "", $content)));
            } elseif ($rules['end_of_line'] === 'cr') {
                $eols = count(str_split(preg_replace("/[^\r]/", "", $content)));
            } elseif ($rules['end_of_line'] === 'crlf') {
                $eols = count(str_split(preg_replace("/[^\r\n]/", "", $content)));
            }

            if (isset($rules['insert_final_newline']) && $rules['insert_final_newline']) {
                if ($eols !== $lineNumbers + 1) {
                    Logger::getInstance()->addError('Not all lines have the correct end of line character!', $file);
                }
            } else {
                if ($eols !== $lineNumbers) {
                    Logger::getInstance()->addError('Not all lines have the correct end of line character!', $file);
                }
            }
        }
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
    protected function getFiles($fileGlobs, $dots, $excludedPattern)
    {
        $files = array();
        foreach ($fileGlobs as $fileGlob) {
            /* if the glob is only a file */
            /* add it to the file array an continue the loop */
            if (is_file($fileGlob)) {
                if (!in_array($fileGlob, $files)) {
                    array_push($files, $fileGlob);
                }

                continue;
            }

            $dirPattern = pathinfo($fileGlob, PATHINFO_DIRNAME);
            $fileType = pathinfo($fileGlob, PATHINFO_EXTENSION);

            if (is_dir($dirPattern)) {
                $objects = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dirPattern));
                foreach ($objects as $fileName => $object) {
                    /* . and .. */
                    if (!$this->isSpecialDir($fileName) &&
                        /* filter for dotfiles */
                        ($dots || strpos($fileName, './'))) {
                        if ($fileType && $fileType === pathinfo($fileName, PATHINFO_EXTENSION)) {
                            /* if I not specify a file extension as argv I get files twice */
                            if (!in_array($fileName, $files)) {
                                array_push($files, $fileName);
                            }
                        } elseif (!strlen($fileType)) {
                            /* if I not specify a file extension as argv I get files twice */
                            if (!in_array($fileName, $files)) {
                                array_push($files, $fileName);
                            }
                        }
                    }
                }
            }
        }

        if ($excludedPattern) {
            return $this->filterFiles($files, $excludedPattern);
        } else {
            return $files;
        }
    }

    /**
     * Filter files for excluded paths
     *
     * @param array $files
     * @param array|string $excludedPatterPattern
     * @return array
     */
    protected function filterFiles($files, $excludedPattern)
    {
        $filteredFiles = [];

        foreach ($files as $file) {
            if (preg_match($excludedPattern, $file) != 1) {
                array_push($filteredFiles, $file);
            }
        }

        return $filteredFiles;
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
        printf('-d, --dots' . PHP_EOL);
        printf("\tuse this flag if you want to also include dotfiles/dotdirectories" . PHP_EOL);
        printf('-e <PATTERN>, --exclude <PATTERN>' . PHP_EOL);
        printf("\tstring or regex to filter files which should not be checked" . PHP_EOL);
    }
}
