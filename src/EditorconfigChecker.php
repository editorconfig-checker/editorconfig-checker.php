<?php
namespace EditorconfigChecker;

use EditorconfigChecker\Cli;

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

if (!isset($argv)) {
    throw new \LogicException('Missing $argv, check your register_argc_argv setting');
}

$result = Cli::run($argv);

exit($result);
