<?php

namespace MStruebing\EditorconfigChecker\Cli;

use MStruebing\EditorconfigChecker\Cli\Logger;

class Cli
{
    const DEFAULT_INDENT_STYLE = 'tab';

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
            throw new \Exception('ERROR: No .editorconfig found');
        }

        $files = $this->getFiles($argv);

        $this->checkFiles($editorconfig, $files);
    }

    protected function checkFiles($editorconfig, $files)
    {
        foreach ($files as $file) {
            if (isset($file[0])) {
                $rules = $this->getRulesForFiletype($editorconfig, $file[0]);
                $this->processCheckForSingleFile($rules, $file[0]);
            }
        }
    }

    protected function processCheckForSingleFile($rules, $file)
    {
        $content = file($file);
        $lastIndentSize = null;

        foreach ($content as $lineNumber => $line) {
            $lastIndentSize = $this->checkForIndentation($rules, $line, $lineNumber, $lastIndentSize, $file);
            $this->checkForTrailingWhitespace($rules, $file, $content);
        }

        $this->checkForFinalNewline($rules, $file, $content);
    }

    protected function checkForIndentation($rules, $line, $lineNumber, $lastIndentSize, $file)
    {
        if (isset($rules['indent_style']) && $rules['indent_style'] === 'space') {
            $lastIndentSize = $this->checkForSpace($rules, $line, $lineNumber, $lastIndentSize, $file);
        } elseif (isset($rules['indent_style']) && $rules['indent_style'] === 'tab') {
            $lastIndentSize = $this->checkForTab($rules, $line, $lineNumber, $lastIndentSize, $file);
        }

        return $lastIndentSize;
    }

    protected function checkForSpace($rules, $line, $lineNumber, $lastIndentSize, $file)
    {
        preg_match('/^( +)/', $line, $matches);

        if (isset($matches[1])) {
            $indentSize = strlen($matches[1]);

            /* check if the indentation size could be a valid one */
            if ($indentSize % $rules['indent_size'] !== 0) {
                throw new \Exception(
                    'The file:'
                    . $file
                    .  ' does not start with the right amount of spaces at line '
                    . ($lineNumber + 1)
                );
            }

            /* because the following example would not work I have to check it this way */
            /* ... maybe it should not? */
            /* if (xyz) */
            /* { */
            /*     throw new Exception('hello */
            /*         world');  <--- this is the critial part */
            /* } */
            if (isset($lastIndentSize) && ($indentSize - $lastIndentSize) > $rules['indent_size']) {
                throw new \Exception(
                    'The indentation size of your lines '
                    . $lineNumber
                    . ' and '
                    . ($lineNumber + 1)
                    . ' in your file: '
                    . $file
                    . ' does not have the right relationship'
                );
            }

            $lastIndentSize = $indentSize;
        } else { /* if no matching leading spaces found check if tabs are there instead */
            preg_match('/^(\t+)/', $line, $matches);
            if (isset($matches[1])) {
                throw new \Exception('Your file ' . $file . ' has the wrong indentation type');
            }
        }

        if (!isset($indentSize)) {
            $indentSize = null;
        }

        return $indentSize;
    }

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
                throw new \Exception(
                    'The indentation size of your lines '
                    . $lineNumber
                    . ' and '
                    . ($lineNumber + 1)
                    . ' in your file: '
                    . $file
                    . ' does not have the right relationship'
                );
            }

            $lastIndentSize = $indentSize;
        } else { /* if no matching leading tabs found check if spaces are there instead */
            preg_match('/^( +)/', $line, $matches);
            if (isset($matches[1])) {
                throw new \Exception('Your line ' . $line . ' in file ' . $file . ' has the wrong indentation type');
            }
        }

        if (!isset($indentSize)) {
            $indentSize = null;
        }

        return $indentSize;
    }

    protected function checkForTrailingWhitespace($rules, $line, $lineNumber)
    {
        if (isset($rules['trim_trailing_whitespace']) && $rules['trim_trailing_whitespace']) {
            preg_match('/^.*\S$/', $line, $matches);

            if (isset($matches[1])) {
                throw new \Exception('Your file ' . $file . ' does not have trimmed whitespace on line ' . $lineNumber);
            }
        }
    }

    protected function checkForFinalNewline($rules, $file, $content)
    {
        if (isset($rules['insert_final_newline']) && $rules['insert_final_newline']) {
            $lastLine = $content[count($content) - 1];
            preg_match('/(.*\n\Z)/', $lastLine, $matches);

            if (!isset($matches[1])) {
                throw new \Exception('Your file ' . $file . ' does not has a final newline.');
            }
        }
    }

    protected function getRulesForFiletype($editorconfig, $file)
    {
        $fileType = pathinfo($file, PATHINFO_EXTENSION);

        /* temporary, dirty hack for makefile */
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

    protected function getEditorconfigRules($editorconfig, $fileType)
    {
        $globalRules = [];
        $ftRules = [];
        foreach ($editorconfig as $key => $value) {
            if ($key === '*') {
                $globalRules = $value;
            }
            if (strpos($key, $fileType) !== false) {
                $ftRules = $value;
            }
        }

        return array_merge($globalRules, $ftRules);
    }

    protected function getFiles($fileGlobs)
    {
        $files = array();
        foreach ($fileGlobs as $fileGlob) {
            array_push($files, glob($fileGlob, GLOB_BRACE));
        }

        return $files;
    }

    protected function printUsage()
    {
        echo "HELLO WORLD";
        printf("USAGE:\n");
        printf("DOO SO\n");
    }
}
