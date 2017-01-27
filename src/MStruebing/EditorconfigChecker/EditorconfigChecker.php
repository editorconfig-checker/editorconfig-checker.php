<?php

namespace MStruebing\EditorconfigChecker;

use MStruebing\EditorconfigChecker\Cli\Cli;

$cliPath = dirname(__FILE__) . '/Cli/Cli.php';

if (is_file($cliPath)) {
    include_once $cliPath;
} else {
    throw new \Exception('The CLI-script can not be found. Contact the maintainer.');
}

$cli = new Cli();
/* The first paaram is only the program-name */
array_shift($argv);
$cli->run($argv);
