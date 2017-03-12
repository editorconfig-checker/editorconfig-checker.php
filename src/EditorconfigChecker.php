<?php

namespace MStruebing\EditorconfigChecker;

use MStruebing\EditorconfigChecker\Cli\Cli;
use MStruebing\EditorconfigChecker\Cli\Logger;

spl_autoload_register(function ($class) {
    $newClass = str_replace('\\', '/', $class);
    require_once($newClass . '.php');
});

array_shift($argv);

$shortOpts = 'hde:';
$longOpts  = ['help', 'dots', 'exclude:'];
$options = getopt($shortOpts, $longOpts);

foreach ($options as $option) {
    if ($option) {
        array_shift($argv);
    }

    array_shift($argv);
}

$cli = new Cli();
$cli->run($options, $argv);

$logger = Logger::getInstance();
if ($count = $logger->countErrors()) {
    $logger->printErrors();
    $count < 255 ? exit($count) : exit(255);
} else {
    exit(0);
}
