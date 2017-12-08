<?php
namespace EditorconfigChecker\Editorconfig;

use Webmozart\Glob\Glob;
use Webmozart\PathUtil\Path;

class Editorconfig
{
    /**
     * Returns the editorconfig of a given path as an array
     *
     * @param string $editorconfigPath
     * @return array
     */
    protected function getRulesAsArray(string $editorconfigPath) : array
    {
        return parse_ini_file($editorconfigPath, true);
    }

    /**
     * Merge editorconfig rules for a given file
     * from an array of editorconfig rules
     *
     * @param string $fileName
     * @param array $editorconfig
     * @return array
     */
    protected function mergeRulesForFile(string $fileName, array $editorconfig) : array
    {
        return array_reduce(array_keys($editorconfig), function ($carry, $pattern) use ($editorconfig, $fileName) {
            $rules = $editorconfig[$pattern];

            return $pattern === 'root' ? ['root' => $editorconfig[$pattern]] : (
                Glob::match(sprintf('%s/%s', getcwd(), $fileName), Path::makeAbsolute('**/' . $pattern, getcwd())) ?
                    array_merge($carry, $rules) : $carry
            );
        }, []);
    }

    /**
     * Return the nearest matching editorconfig rules
     *
     * @param string $filePath
     * @param string $fileBasename
     * @param string $rootDir
     * @return array
     */
    protected function getNearestMatchingEditorconfigRules(
        string $filePath,
        string $fileBasename,
        string $rootDir
    ) : array {
        $currentPath = $filePath;
        $editorconfig = array();

        do {
            if (is_file($currentPath . '/.editorconfig')) {
                $rawEditorconfig = $this->getRulesAsArray($currentPath . '/.editorconfig');
                $editorconfig = $this->mergeRulesForFile($fileBasename, $rawEditorconfig);
            }
            $currentPath = dirname($currentPath);
        } while (strpos($currentPath, $rootDir) === 0 && sizeof($editorconfig) === 0);

        // If no editorconfig was found along the way, return an empty array
        return $editorconfig ? $editorconfig : [];
    }

    /**
     * Returns the editorconfig rules for a given file
     *
     * @param string $fileName
     * @param string $rootDir
     * @return array
     */
    public function getRulesForFile(string $fileName, string $rootDir) : array
    {
        $pathinfo = pathinfo($rootDir . '/' . $this->normalizeFileName($fileName));
        $filePath = $pathinfo['dirname'];
        $fileBasename = $pathinfo['basename'];

        $rules = $this->getNearestMatchingEditorconfigRules($filePath, $fileBasename, $rootDir);

        return $rules;
    }

    /**
     * Normalizes the filename in terms of stripping the optional
     * leading ./ from the fileName
     *
     * @param string $fileName
     * @return string
     */
    private function normalizeFileName(string $fileName) : string
    {
        if (strpos($fileName, './') === 0) {
            return substr($fileName, 2);
        }

        return $fileName;
    }
}
