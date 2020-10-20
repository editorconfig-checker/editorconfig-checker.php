<?php

namespace EditorconfigChecker;

use EditorconfigChecker\Utilities;

define('CORE_VERSION', '2.2.0');

class Cli
{
    /**
     * Entry point of this class to invoke all needed steps
     */
    public static function run(array $arguments): int
    {
        $releaseName = Utilities::getReleaseName();
        $binaryPath = Utilities::getBinaryPath();

        if (!is_file($binaryPath)) {
            Utilities::cleanup();
            if (!Utilities::downloadReleaseArchive($releaseName, CORE_VERSION)) {
                printf('ERROR: Can not download the archive%s', PHP_EOL);
                return 1;
            }

            if (!Utilities::extractReleaseArchive($releaseName)) {
                printf('ERROR: can not extract the archive%s', PHP_EOL);
                return 1;
            }

            Utilities::cleanup();
        }

        array_shift($arguments);
        $args = Utilities::constructStringFromArguments($arguments);
        system($binaryPath . $args, $result);

        return $result;
    }
}
