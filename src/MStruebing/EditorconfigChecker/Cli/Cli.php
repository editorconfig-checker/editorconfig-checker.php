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
    public function run($argv)
    {
        /* We need files to check */
        if (count($argv) === 0) {
            $this->printUsage();
            return;
        }

        $rootDir = getcwd();
        $editorconfigPath = $rootDir . '/.editorconfig';

        if (is_file($editorconfigPath)) {
            $editorconfig = parse_ini_file($rootDir . '/.editorconfig', true);
        } else {
            $this->logger->addError('No .editorconfig found');
        }

        $files = $this->getFiles($argv);

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
            if (isset($file[0])) {
                $rules = $this->getRulesForFiletype($editorconfig, $file[0]);
                $this->processCheckForSingleFile($rules, $file[0]);
            }
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
        $this->checkForFinalNewline($rules, $file, $content);
        $this->checkForLineEnding($rules, $file, $lineNumber);
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
                $this->logger->addError('Not the right amount of spaces', $file, $lineNumber);
            }

            /* because the following example would not work I have to check it this way */
            /* ... maybe it should not? */
            /* if (xyz) */
            /* { */
            /*     throw new Exception('hello */
            /*         world');  <--- this is the critial part */
            /* } */
            if (isset($lastIndentSize) && ($indentSize - $lastIndentSize) > $rules['indent_size']) {
                $this->logger->addError('Not the right relation of spaces between lines', $file, $lineNumber);
            }

            $lastIndentSize = $indentSize;
        } else { /* if no matching leading spaces found check if tabs are there instead */
            preg_match('/^(\t+)/', $line, $matches);
            if (isset($matches[1])) {
                $this->logger->addError('Wrong indentation type', $file, $lineNumber);
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
                $this->logger->addError('Not the right relation of tabs between lines', $file, $lineNumber);
            }

            $lastIndentSize = $indentSize;
        } else { /* if no matching leading tabs found check if spaces are there instead */
            preg_match('/^( +)/', $line, $matches);
            if (isset($matches[1])) {
                $this->logger->addError('Wrong indentation type', $file, $lineNumber);
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
                $this->logger->addError('Trailing whitespace', $file, $lineNumber);
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
     *
     * @param array $fileGlobs
     * @return array
     */
    protected function getFiles($fileGlobs)
    {
        $files = array();
        foreach ($fileGlobs as $fileGlob) {
            $file = glob($fileGlob, GLOB_BRACE + GLOB_MARK);
            var_dump($files);
            if (substr($file[0], -1) !== '/') {
                array_push($files, $file);
            }
        }

        return $files;
    }

    /**
     * Prints the usage
     *
     * @return void
     */
    protected function printUsage()
    {
        printf('Usage:' . PHP_EOL);
        printf('editorconfig-checker <FILE>|<FILEGLOB>' . PHP_EOL);
    }
}
