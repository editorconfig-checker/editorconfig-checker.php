<?php

namespace MStruebing\EditorconfigChecker\Cli\Logger;

final class Logger
{
    public static function getInstance()
    {
        if (!isset(static::$instance)) {
            static::$instance = new static;
        }
        return static::$instance;
    }
}
