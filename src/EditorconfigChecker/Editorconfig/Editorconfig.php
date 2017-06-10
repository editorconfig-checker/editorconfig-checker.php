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
    protected function getRulesAsArray($editorconfigPath)
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
    protected function mergeRulesForFile($fileName, $editorconfig)
    {
        return array_reduce(array_keys($editorconfig), function ($carry, $pattern) use ($editorconfig, $fileName) {
            $rules = $editorconfig[$pattern];
            $fileName = substr($fileName, 2);

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
     */
    protected function getNearestMatchingEditorconfigRules($filePath, $fileBasename, $rootDir)
    {
        $currentPath = $filePath;
        $editorconfig = null;

        do {
            if (is_file($currentPath . '/.editorconfig')) {
                $editorconfig = $this->getRulesAsArray($currentPath . '/.editorconfig');
                $editorconfig = $this->mergeRulesForFile($fileBasename, $editorconfig);
            }
            $currentPath = dirname($currentPath);
        } while (strpos($currentPath, $rootDir) === 0 && sizeof($editorconfig) === 0);

        return $editorconfig;
    }

    /**
     * Returns the editorconfig rules for a given file
     *
     * @param string $fileName
     * @return array
     */
    public function getRulesForFile($fileName)
    {
        $pathinfo = pathinfo(getcwd() . substr($fileName, 1));
        $rootDir = getcwd();
        $filePath = $pathinfo['dirname'];
        $fileBasename = $pathinfo['basename'];

        $rules = $this->getNearestMatchingEditorconfigRules($filePath, $fileBasename, $rootDir);

        return $rules;
    }
}
