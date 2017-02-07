<?php

namespace MStruebing\EditorconfigChecker\Cli;

class Cli
{
    const DEFAULT_INDENT_STYLE = 'tab';

    /**
     * @var MStruebing\EditorconfigChecker\Cli\Logger
     */
    protected $logger;

    public function __construct($logger)
    {
        $this->logger = $logger;
    }

    /**
     * Entry point of this class to invoke all needed steps
     *
     * @param array $argv
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

        if (is_file($editorconfigPath)) {
            $editorconfig = parse_ini_file($rootDir . '/.editorconfig', true);
        } else {
            $this->logger->addError('No .editorconfig found');
            return;
        }

        isset($options['dots']) || isset($options['d']) ? $dots = true : $dots = false;
        $excludedPathParts = $this->getExcludedPathParts($options);

        $files = $this->getFiles($fileGlobs, $dots, $excludedPathParts);

        if (count($files) > 0) {
            $this->checkFiles($editorconfig, $files);
        }
    }

    /**
     * Loop over files and get the editorconfig rules for this file and invokes the check
     *
     * @param array $editorconfig
     * @param array $files
     * @return void
     */
    protected function checkFiles($editorconfig, $files)
    {
        foreach ($files as $file) {
            $rules = $this->getRulesForFiletype($editorconfig, $file);
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
            $this->checkForTrailingWhitespace($rules, $file, $content);
        }

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
     * @return void
     */
    protected function checkForIndentation($rules, $line, $lineNumber, $lastIndentSize, $file)
    {
        if (isset($rules['indent_style']) && $rules['indent_style'] === 'space') {
            $lastIndentSize = $this->checkForSpace($rules, $line, $lineNumber, $lastIndentSize, $file);
        } elseif (isset($rules['indent_style']) && $rules['indent_style'] === 'tab') {
            $lastIndentSize = $this->checkForTab($rules, $line, $lineNumber, $lastIndentSize, $file);
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
                $this->logger->addError('Not the right amount of spaces', $file, $lineNumber + 1);
            }

            /* because the following example would not work I have to check it this way */
            /* ... maybe it should not? */
            /* if (xyz) */
            /* { */
            /*     throw new Exception('hello */
            /*         world');  <--- this is the critial part */
            /* } */
            if (isset($lastIndentSize) && ($indentSize - $lastIndentSize) > $rules['indent_size']) {
                $this->logger->addError('Not the right relation of spaces between lines', $file, $lineNumber + 1);
            }

            $lastIndentSize = $indentSize;
        } else { /* if no matching leading spaces found check if tabs are there instead */
            preg_match('/^(\t+)/', $line, $matches);
            if (isset($matches[1])) {
                $this->logger->addError('Wrong indentation type', $file, $lineNumber + 1);
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
                $this->logger->addError('Not the right relation of tabs between lines', $file, $lineNumber + 1);
            }

            $lastIndentSize = $indentSize;
        } else { /* if no matching leading tabs found check if spaces are there instead */
            preg_match('/^( +)/', $line, $matches);
            if (isset($matches[1])) {
                $this->logger->addError('Wrong indentation type', $file, $lineNumber + 1);
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
    protected function checkForTrailingWhitespace($rules, $line, $lineNumber)
    {
        if (isset($rules['trim_trailing_whitespace']) && $rules['trim_trailing_whitespace']) {
            preg_match('/^.*\S$/', $line, $matches);

            if (isset($matches[1])) {
                $this->logger->addError('Trailing whitespace', $file, $lineNumber + 1);
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
                $this->logger->addError('Missing final newline', $file);
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
                    $this->logger->addError('Not all lines have the correct end of line character!', $file);
                }
            } else {
                if ($eols !== $lineNumbers) {
                    $this->logger->addError('Not all lines have the correct end of line character!', $file);
                }
            }
        }
    }

    /**
     * Returns the editorconfig rules for a file
     *
     * @param array $editorconfig
     * @param string $file
     * @return array
     */
    protected function getRulesForFiletype($editorconfig, $file)
    {
        $fileType = pathinfo($file, PATHINFO_EXTENSION);

        /* temporary ;), dirty hack for makefile */
        if (!$fileType && $file === 'Makefile') {
            $fileType = 'Makefile';
        }

        $ftRules = $this->getEditorconfigRules($editorconfig, $fileType);

        if ($ftRules !== false) {
            $rules = $ftRules;
        }

        if (!isset($rules['indent_style'])) {
            $rules['indent_style'] = self::DEFAULT_INDENT_STYLE;
        }

        return $rules;
    }

    /**
     * Returns an array of the merged rules from a specific filetype and global ones
     *
     * @param array $editorconfig
     * @param string $fileType
     * @return array
     */
    protected function getEditorconfigRules($editorconfig, $fileType)
    {
        $globalRules = [];
        $ftRules = [];
        foreach ($editorconfig as $key => $value) {
            if ($key === '*') {
                $globalRules = $value;
            }
            /* files with no extension have an empty filetype */
            if (strlen($fileType) && strpos($key, $fileType) !== false) {
                $ftRules = $value;
            }
        }

        return array_merge($globalRules, $ftRules);
    }

    /**
     * Returns an array of files matching the fileglobs
     * if dots is true dotfiles will be added too otherwise
     * dotfiles will be ignored
     *
     * @param array $fileGlobs
     * @param boolean $dots
     * @return array
     */
    protected function getFiles($fileGlobs, $dots, $excludedPathParts)
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
                        ($dots || pathinfo($fileName, PATHINFO_BASENAME)[0] !== '.' )) {
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

        return $this->filterFiles($files, $excludedPathParts);
    }

    /**
     * Filter files for excluded paths
     *
     * @param array $files
     * @param array|string $excludedPathParts
     * @return array
     */
    protected function filterFiles($files, $excludedPathParts)
    {
        $filteredFiles = [];

        if (is_array($excludedPathParts)) {
            $pattern = '/' . implode('|', $excludedPathParts) . '/';
        } else {
            $pattern = '/' . $excludedPathParts . '/';
        }

        foreach ($files as $file) {
            if (preg_match($pattern, $file) != 1) {
                array_push($filteredFiles, $file);
            }
        }
        var_dump($filteredFiles);

        return $filteredFiles;
    }

    /**
     * Get the excluded path parts from the options
     *
     * @param array $options
     * @return array
     */
    protected function getExcludedPathParts($options)
    {
        if (isset($options['e']) && !isset($options['exclude'])) {
            $excludedPathParts = $options['e'];
        } elseif (!isset($options['e']) && isset($options['exclude'])) {
            $excludedPathParts = $options['exclude'];
        } elseif (isset($options['e']) && isset($options['exclude'])) {
            if (is_array($options['e']) && is_array($options['exclude'])) {
                $excludedPathParts = array_merge($options['e'], $options['exclude']);
            } elseif (is_array($options['e']) && !is_array($options['exclude'])) {
                array_push($options['e'], $options['exclude']);
                $excludedPathParts = $options['e'];
            } elseif (!is_array($options['e']) && is_array($options['exclude'])) {
                array_push($options['exclude'], $options['e']);
                $excludedPathParts = $options['exclude'];
            } else {
                $excludedPathParts = [$options['e'], $options['exclude']];
            }
        }

        return $excludedPathParts;
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
        printf("\tuse this flag if you want to also include dotfiles" . PHP_EOL);
        printf('-e <PATTERN>, --exclude <PATTERN>' . PHP_EOL);
        printf("\tstring or regex to filter files which should not be checked" . PHP_EOL);
    }
}
