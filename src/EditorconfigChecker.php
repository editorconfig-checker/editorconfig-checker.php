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

$result = Cli::run($argv);

exit($result);
