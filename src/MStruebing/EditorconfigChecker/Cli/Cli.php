<?php

namespace MStruebing\EditorconfigChecker\Cli;

class Cli
{
    const DEFAULT_INDENT_STYLE = 'tab';

    public function run($argv)
    {
        // We need files to check
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

                    if ($indentSize % $rules['indent_size'] !== 0) {
                        throw new \Exception("The file:"
                            . $file
                            .
                            ' does not start with the right amount of spaces at line '
                            . ($lineNumber + 1));
                    }
                }
            }
        } elseif ($rules['indent_style'] === 'tab') {
        }
    }

    protected function getRulesForFiletype($editorconfig, $file)
    {
        $fileType = pathinfo($file, PATHINFO_EXTENSION);
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
        $rules = false;

        foreach ($editorconfig as $key => $value) {
            if (strpos($key, $fileType) !== false) {
                $rules = $value;
            }
        }

        return $rules;
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
