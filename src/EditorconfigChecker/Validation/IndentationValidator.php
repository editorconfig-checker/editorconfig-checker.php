<?php

namespace EditorconfigChecker\Validation;

use EditorconfigChecker\Cli\Logger;

class IndentationValidator
{
    /**
     * Determines if the file is to check for spaces or tabs
     *
     * @param array $rules
     * @param string $line
     * @param int $lineNumber
     * @param string $filename
     * @return int
     */
    public static function validate($rules, $line, $lineNumber, $filename)
    {
        $valid = true;

        if (isset($rules['indent_style']) && $rules['indent_style'] === 'space') {
            $valid = IndentationValidator::validateSpace($rules, $line, $lineNumber, $filename);
        } elseif (isset($rules['indent_style']) && $rules['indent_style'] === 'tab') {
            $valid = IndentationValidator::validateTab($rules, $line, $lineNumber, $filename);
        }

        return $valid;
    }

    /**
     * Processes indentation check for spaces
     *
     * @param array $rules
     * @param string $line
     * @param int $lineNumber
     * @param string $filename
     * @return boolean
     */
    protected static function validateSpace($rules, $line, $lineNumber, $filename)
    {
        $valid = true;
        preg_match('/^( +)/', $line, $matches);

        if (isset($matches[1])) {
            $indentSize = strlen($matches[1]);

            /* check if the indentation size could be a valid one */
            /* the * is for block comments */
            if ($indentSize % $rules['indent_size'] !== 0 && $line[$indentSize] !== '*') {
                Logger::getInstance()->addError(
                    'Not the right amount of spaces',
                    $filename,
                    $lineNumber + 1
                );

                $valid = false;
            }

            if ($line[$indentSize] === "\t") {
                Logger::getInstance()->addError(
                    'Mixed indentation',
                    $filename,
                    $lineNumber + 1
                );

                $valid = false;
            }
        } else { /* if no matching leading spaces found check if tabs are there instead */
            preg_match('/^(\t+)/', $line, $matches);
            if (isset($matches[1])) {
                Logger::getInstance()->addError(
                    'Wrong indentation type',
                    $filename,
                    $lineNumber + 1
                );

                $valid = false;
            }
        }

        return $valid;
    }

    /**
     * Processes indentation check for tabs
     *
     * @param array $rules
     * @param string $line
     * @param int $lineNumber
     * @param string $filename
     * @return boolean
     */
    protected static function validateTab($line, $lineNumber, $filename)
    {
        $valid = true;
        preg_match('/^(\t+)/', $line, $matches);

        if (isset($matches[1])) {
            $indentSize = strlen($matches[1]);

            if (substr($line, $indentSize, 1) === ' ' && substr($line, $indentSize + 1, 1) !== '*') {
                Logger::getInstance()->addError(
                    'Mixed indentation',
                    $filename,
                    $lineNumber + 1
                );

                $valid = false;
            }
        } else { /* if no matching leading tabs found check if spaces are there instead */
            preg_match('/^( +)/', $line, $matches);
            if (isset($matches[1]) && strpos($line, ' *') !== 0) {
                Logger::getInstance()->addError(
                    'Wrong indentation type',
                    $filename,
                    $lineNumber + 1
                );

                $valid = false;
            }
        }

        return $valid;
    }
}
