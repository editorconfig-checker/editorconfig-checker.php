<?php

namespace MStruebing\EditorconfigChecker\Cli;

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
            /* @TODO flatten the array before */
            /* @TODO do it the other way around -> iterate over editorconfig */
            if (isset($file[0])) {
                $rules = $this->getRulesForFiletype($editorconfig, $file[0]);
                $this->processCheckForSingleFile($rules, $file[0]);
            }
        }
    }

    protected function processCheckForSingleFile($rules, $file)
    {
        $content = file($file);

        if ($rules['indent_style'] === 'space') {
            foreach ($content as $lineNumber => $line) {
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
            }
        } elseif ($rules['indent_style'] === 'tab') {
            foreach ($content as $lineNumber => $line) {
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
                    preg_match('/^( +)/', $line, $matches);
                    if (isset($matches[1])) {
                        throw new \Exception('Your file ' . $file . ' has the wrong indentation type');
                    }
                }
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
