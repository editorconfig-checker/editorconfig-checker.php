<?php

namespace EditorconfigChecker\Editorconfig;

class Editorconfig
{
    /**
     * Returns the editorconfig as an array
     *
     * @param string $editorconfigPath
     * @return array
     */
    public function getRulesAsArray($editorconfigPath)
    {
        return parse_ini_file($editorconfigPath, true);
    }

    /**
     * Returns the editorconfig rules for a file
     *
     * @param array $editorconfig
     * @param string $file
     * @return array
     */
    public function getRulesForFiletype($editorconfig, $file)
    {
        $fileType = pathinfo($file, PATHINFO_EXTENSION);

        /* temporary ;), dirty hack for makefile */
        if (!$fileType && $file === 'Makefile') {
            $fileType = 'Makefile';
        }

        $ftRules = $this->getEditorconfigRules($editorconfig, $fileType);

        if ($ftRules !== false) {
            $rules = $ftRules;
        }

        return $rules;
    }

    /**
     * Returns an array of the merged rules from a specific filetype and global ones
     *
     * @param array $editorconfig
     * @param string $fileType
     * @return array
     */
    public function getEditorconfigRules($editorconfig, $fileType)
    {
        $globalRules = [];
        $ftRules = [];

        foreach ($editorconfig as $key => $value) {
            if ($key === '*') {
                $globalRules = $value;
            }
            /* files with no extension have an empty filetype */
            if (strlen($fileType) && strpos($key, $fileType) !== false) {
                $ftRules = $value;
            }
        }

        return array_merge($globalRules, $ftRules);
    }
}
