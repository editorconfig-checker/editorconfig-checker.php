<?php

namespace MStruebing\EditorconfigChecker\Validation;

use MStruebing\EditorconfigChecker\Cli\Logger;

class FinalNewlineValidator implements ValidatorInterface
{
    public function validate($file)
    {
        return true;
    }
}
