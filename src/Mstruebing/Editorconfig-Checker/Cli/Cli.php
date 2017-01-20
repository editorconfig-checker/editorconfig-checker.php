<?php

class Cli
{
    public function run($argv)
    {
        if (count($argv) == 1) {
            $this->printUsage();
        }
    }

    protected function printUsage() {
        echo "HELLO WORLD";
        printf("USAGE:\n");
        printf("DOO SO\n");
    }
}

