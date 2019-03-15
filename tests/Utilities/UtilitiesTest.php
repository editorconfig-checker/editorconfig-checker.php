<?php

namespace Test;

use PHPUnit\Framework\TestCase;
use EditorconfigChecker\Utilities as U;

final class UtilitiesTest extends TestCase
{
    public function testConstructStringFromArguments(): void
    {
        $this->assertEquals('', U::constructStringFromArguments([]));
        $this->assertEquals(' --help', U::constructStringFromArguments(['--help']));
        $this->assertEquals(' --help --verbose', U::constructStringFromArguments(['--help', '--verbose']));
    }
}
