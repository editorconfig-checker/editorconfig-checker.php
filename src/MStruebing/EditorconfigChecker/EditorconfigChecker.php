<?php

namespace MStruebing\EditorconfigChecker;

use MStruebing\EditorconfigChecker\Cli\Cli;
use MStruebing\EditorconfigChecker\Cli\Logger;

$cliPath = dirname(__FILE__) . '/Cli/Cli.php';
$loggerPath = dirname(__FILE__) . '/Cli/Logger.php';

if (is_file($cliPath)) {
    include_once $cliPath;
} else {
    throw new \Exception('The CLI-class can not be found. Contact the maintainer.');
}

if (is_file($loggerPath)) {
    include_once $loggerPath;
} else {
    throw new \Exception('The Logger-class can not be found. Contact the maintainer.');
}

/* The first paaram is only the program-name */
array_shift($argv);

$logger = new Logger();

$cli = new Cli($logger);
$cli->run($argv);

if ($logger->countErrors()) {
    $logger->printErrors();
    exit(1);
} else {
    exit(0);
}
