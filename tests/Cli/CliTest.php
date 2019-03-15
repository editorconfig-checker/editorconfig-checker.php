<?php

use PHPUnit\Framework\TestCase;
use EditorconfigChecker\Cli;

final class CliTest extends TestCase
{
    protected function basePath(): string
    {
        return sprintf("%s/../../src/EditorconfigChecker.php", dirname(__FILE__));
    }

    public function testRun(): void
    {
        $this->assertEquals(Cli::run([$this->basePath()]), 0);

        // TestFiles should fail
        system("cd ./Build && ../bin/ec", $return);
        $this->assertEquals($return, 1);
    }

    public function testFlag(): void
    {
        $this->assertEquals(Cli::run([$this->basePath(), '--help']), 0);
        $this->assertEquals(Cli::run([$this->basePath(), '--version']), 0);
    }
}
