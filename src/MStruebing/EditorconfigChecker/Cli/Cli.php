<?php

namespace MStruebing\EditorconfigChecker\Cli;

class Cli
{
    public function run($argv)
    {
        if (count($argv) == 1) {
            $this->printUsage();
            return;
        }

        $rootDir = getcwd();

        $editorconfig = parse_ini_file($rootDir . '/.editorconfig', true);
        var_dump($editorconfig);
    }

    protected function printUsage()
    {
        echo "HELLO WORLD";
        printf("USAGE:\n");
        printf("DOO SO\n");
    }
}
