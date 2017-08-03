<?php
namespace EditorconfigChecker;

use EditorconfigChecker\Cli\Cli;
use EditorconfigChecker\Cli\Logger;

$paths = [
    __DIR__.'/../vendor/autoload.php',
    __DIR__.'/../../../autoload.php'
];
foreach ($paths as $path) {
    if (file_exists($path)) {
        require_once $path;
        break;
    }
}

array_shift($argv);

$shortOpts = 'ahlde:i';
$longOpts  = ['auto-fix',
    'help',
    'list-files',
    'dotfiles',
    'exclude:',
    'ignore-defaults'
];
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
    $logger->printSuccessMessage();
    exit(0);
}
