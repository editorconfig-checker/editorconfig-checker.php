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
     * Get the nearest editorconfig to the given path
     *
     * @param string $baseDir
     * @return array
     */
    protected function getNearestEditorconfigRulesAsArray($baseDir)
    {
        $baseEditorconfig = $baseDir . '/.editorconfig';
        if (is_file($baseEditorconfig)) {
            return $this->getRulesAsArray($baseEditorconfig);
        } else {
            return $this->getNearestEditorconfigRulesAsArray(dirname($baseDir));
        }
    }

    /**
     * Merge editorconfig rules for a given file
     *
     * @param string $fileName
     * @return array
     */
    protected function mergeRulesForFile($fileName)
    {
        $editorconfig = $this->getNearestEditorconfigRulesAsArray(getcwd() . pathinfo(substr($fileName, 1))['dirname']);

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
     * Returns the editorconfig rules for a given file
     * until root=true
     *
     * @param string $fileName
     * @return array
     */
    public function getRulesForFile($fileName)
    {
        $rules = [];

        do {
            $rules = array_merge($this->mergeRulesForFile($fileName), $rules);
            $fileName = dirname(pathinfo($fileName)['dirname']) . pathinfo($fileName)['basename'];
        } while (!isset($rules['root']));

        return $rules;
    }
}
