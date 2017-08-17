<?php

namespace EditorconfigChecker\Validation;

use EditorconfigChecker\Cli\Logger;
use EditorconfigChecker\Fix\IndentationFix;

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
    public function validate(array $rules, string $line, int $lineNumber, string $filename, bool $autoFix) : bool
    {
        $valid = true;

        if (isset($rules['indent_style']) && $rules['indent_style'] === 'space') {
            $valid = $this->validateSpace($rules, $line, $lineNumber, $filename, $autoFix);
        } elseif (isset($rules['indent_style']) && $rules['indent_style'] === 'tab') {
            $valid = $this->validateTab($line, $lineNumber, $filename);
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
    protected function validateSpace(array $rules, string $line, int $lineNumber, string $filename) : bool
    {
        preg_match('/^( +)/', $line, $matches);

        if (isset($matches[1])) {
            $indentSize = strlen($matches[1]);

            /* check if the indentation size could be a valid one */
            /* the * is for block comments */
            if ($indentSize % $rules['indent_size'] !== 0 && $line[$indentSize] !== '*') {
                Logger::getInstance()->addError(
                    'Wrong amount of spaces(found '
                        . $indentSize
                        . ' expected multiple of '
                        . $rules['indent_size']
                        . ')',
                    $filename,
                    $lineNumber + 1
                );

                return false;
            }

            if ($line[$indentSize] === "\t") {
                Logger::getInstance()->addError(
                    'Mixed indentation(expected multiple of ' . $rules['indent_size'] . ' spaces)',
                    $filename,
                    $lineNumber + 1
                );

                return false;
            }
        } else { /* if no matching leading spaces found check if tabs are there instead */
            preg_match('/^(\t+)/', $line, $matches);
            if (isset($matches[1])) {
                Logger::getInstance()->addError(
                    'Wrong indentation type(tabs found, expected spaces(multiple of '
                    . $rules['indent_size'] . '))',
                    $filename,
                    $lineNumber + 1
                );

                $indentationFix = new IndentationFix();
                if ($autoFix && $indentationFix->tabsToSpaces($filename, $lineNumber, $rules['indent_size'])) {
                    Logger::getInstance()->errorFixed();
                }

                return false;
            }
        }

        return true;
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
    protected function validateTab(string $line, int $lineNumber, string $filename) : bool
    {
        preg_match('/^(\t+)/', $line, $matches);

        if (isset($matches[1])) {
            $indentSize = strlen($matches[1]);

            if (substr($line, $indentSize, 1) === ' ' && substr($line, $indentSize + 1, 1) !== '*') {
                Logger::getInstance()->addError(
                    'Mixed indentation',
                    $filename,
                    $lineNumber + 1
                );

                return false;
            }
        } else { /* if no matching leading tabs found check if spaces are there instead */
            preg_match('/^( +)/', $line, $matches);
            if (isset($matches[1]) && strpos($line, ' *') !== 0) {
                Logger::getInstance()->addError(
                    'Wrong indentation type(spaces instead of tabs)',
                    $filename,
                    $lineNumber + 1
                );

                return false;
            }
        }

        return true;
    }
}
