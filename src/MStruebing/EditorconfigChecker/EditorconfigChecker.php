<?php

namespace MStruebing\EditorconfigChecker;

use MStruebing\EditorconfigChecker\Cli\Cli;

$cliPath = dirname(__FILE__) . '/Cli/Cli.php';

if (is_file($cliPath)) {
    include_once $cliPath;
}

$cli = new Cli();
$cli->run($argv);
