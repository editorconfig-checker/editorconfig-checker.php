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
     * @param int $lastIndentSize
     * @param string $file
     * @return int
     */
    public static function validate($rules, $line, $lineNumber, $lastIndentSize, $file)
    {
        if (isset($rules['indent_style']) && $rules['indent_style'] === 'space') {
            $lastIndentSize = IndentationValidator::validateSpace($rules, $line, $lineNumber, $lastIndentSize, $file);
        } elseif (isset($rules['indent_style']) && $rules['indent_style'] === 'tab') {
            $lastIndentSize = IndentationValidator::validateTab($rules, $line, $lineNumber, $lastIndentSize, $file);
        } else {
            $lastIndentSize = 0;
        }

        return $lastIndentSize;
    }

    /**
     * Processes indentation check for spaces
     *
     * @param array $rules
     * @param string $line
     * @param int $lineNumber
     * @param int $lastIndentSize
     * @param string $file
     * @return void
     */
    protected static function validateSpace($rules, $line, $lineNumber, $lastIndentSize, $file)
    {
        preg_match('/^( +)/', $line, $matches);

        if (isset($matches[1])) {
            $indentSize = strlen($matches[1]);

            /* check if the indentation size could be a valid one */
            /* the * is for block comments */
            if ($indentSize % $rules['indent_size'] !== 0 && $line[$indentSize] !== '*') {
                Logger::getInstance()->addError('Not the right amount of spaces', $file, $lineNumber + 1);
            }

            /* because the following example would not work I have to check it this way */
            /* ... maybe it should not? */
            /* if (xyz) */
            /* { */
            /*     throw new Exception('hello */
            /*         world');  <--- this is the critial part */
            /* } */
            if (isset($lastIndentSize) && ($indentSize - $lastIndentSize) > $rules['indent_size']) {
                Logger::getInstance()->addError(
                    'Not the right relation of spaces between lines',
                    $file,
                    $lineNumber + 1
                );
            }

            $lastIndentSize = $indentSize;
        } else { /* if no matching leading spaces found check if tabs are there instead */
            preg_match('/^(\t+)/', $line, $matches);
            if (isset($matches[1])) {
                Logger::getInstance()->addError('Wrong indentation type', $file, $lineNumber + 1);
            }
        }

        if (!isset($indentSize)) {
            $indentSize = null;
        }

        return $indentSize;
    }

    /**
     * Processes indentation check for tabs
     *
     * @param array $rules
     * @param string $line
     * @param int $lineNumber
     * @param int $lastIndentSize
     * @param string $file
     * @return void
     */
    protected static function validateTab($rules, $line, $lineNumber, $lastIndentSize, $file)
    {
        preg_match('/^(\t+)/', $line, $matches);

        if (isset($matches[1])) {
            $indentSize = strlen($matches[1]);

            /* because the following example would not work I have to check it this way */
            /* ... maybe it should not? */
            /* if (xyz) */
            /* { */
            /*     throw new Exception('hello */
            /*         world');  <--- this is the critial part */
            /* } */
            if (isset($lastIndentSize) && ($indentSize - $lastIndentSize) > 1) {
                Logger::getInstance()->addError('Not the right relation of tabs between lines', $file, $lineNumber + 1);
            }

            $lastIndentSize = $indentSize;
        } else { /* if no matching leading tabs found check if spaces are there instead */
            preg_match('/^( +)/', $line, $matches);
            if (isset($matches[1])) {
                Logger::getInstance()->addError('Wrong indentation type', $file, $lineNumber + 1);
            }
        }

        if (!isset($indentSize)) {
            $indentSize = null;
        }

        return $indentSize;
    }
}
