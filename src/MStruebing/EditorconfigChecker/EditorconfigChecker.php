<?php

namespace MStruebing\EditorconfigChecker;

use MStruebing\EditorconfigChecker\Cli\Cli;

class EditorconfigChecker
{
    public function start($argv)
    {
        if (is_file(dirname(__FILE__) . '/Cli/Cli.php')) {
            include_once dirname(__FILE__) . '/Cli/Cli.php';
        } else {

        }

        $cli = new Cli();
        $cli->run($argv);
    }
}

$editorconfigChecker = new EditorconfigChecker();
$editorconfigChecker->start($argv);
