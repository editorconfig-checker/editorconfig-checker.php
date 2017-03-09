<?php

namespace MStruebing\EditorconfigChecker\Validation;

use MStruebing\EditorconfigChecker\Cli\Logger;

class LineEndingValidator implements ValidatorInterface
{
    public function validate($file)
    {
        return true;
    }
}
