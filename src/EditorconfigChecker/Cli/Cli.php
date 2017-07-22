<?php

namespace EditorconfigChecker\Cli;

use EditorconfigChecker\Editorconfig\Editorconfig;
use EditorconfigChecker\Validation\ValidationProcessor;
use Symfony\Component\Finder\Finder;

class Cli
{
    /**
     * Entry point of this class to invoke all needed steps
     *
     * @param array $options
     * @param array $fileGlobs
     * @return void
     */
    public function run($options, $fileGlobs)
    {
        $usage = isset($options['h']) || isset($options['help']);
        $showFiles = isset($options['l']) || isset($options['list-files']);
        $autoFix = isset($options['a']) || isset($options['auto-fix']);

        if ($usage) {
            $this->printUsage();
            return;
        }

        isset($options['dotfiles']) || isset($options['d']) ? $ignoreDotFiles = true : $ignoreDotFiles = false;
        $excludeOptions = $this->getExcludeOptionsFromOptions($options);

        $fileNames = $this->getFileNames($fileGlobs, $ignoreDotFiles, $excludeOptions);
        $fileCount = count($fileNames);

        if ($showFiles) {
            foreach ($fileNames as $fileName) {
                printf('%s' . PHP_EOL, $fileName);
            }
            printf('total: %d files' . PHP_EOL, $fileCount);
        }

        if ($fileCount > 0) {
            ValidationProcessor::validateFiles($fileNames, $autoFix);
        }

        Logger::getInstance()->setFiles($fileCount);
    }

    /**
     * Returns an array of files matching the fileglobs
     * if excludedPattern is provided the files will be filtered
     * for the excludedPattern
     *
     * @param array $fileGlobs
     * @param boolean $ignoreDotFiles
     * @param array $excludeOptions
     * @return array
     */
    protected function getFileNames($fileGlobs, $ignoreDotFiles, $excludeOptions)
    {
        $fileNames = array();
        $finder = new Finder();

        foreach ($excludeOptions['dirs'] as $dir) {
            $finder->notPath($dir);
        }

        foreach ($excludeOptions['files'] as $file) {
            $finder->files()->notName($file);
        }

        if (count($fileGlobs)) {
            foreach ($fileGlobs as $fileGlob) {
                if (is_file($fileGlob)) {
                    array_push($fileNames, $fileGlob);
                    continue;
                }

                $pathinfo = pathinfo($fileGlob);
                $pathinfo['dirname'] !== '.' ? $dirname = $pathinfo['dirname'] : $dirname = getcwd();
                $finder->files()->in($dirname)->ignoreDotFiles($ignoreDotFiles);
            }
        } else {
            $finder->files()->in(getcwd())->ignoreDotFiles($ignoreDotFiles);
        }

        foreach ($finder as $file) {
            if (!in_array($file->getPathName(), $fileNames)) {
                array_push($fileNames, $file->getPathName());
            }
        }

        return $fileNames;
    }

    /**
     * Builds an two dimensional array with
     * array['dirs'] and array['files']
     *
     * @param array $options
     * @return array
     */
    protected function getExcludeOptionsFromOptions($options)
    {
        $excludeOptions['dirs'] = array();
        $excludeOptions['files'] = array();

        /* die; */

        isset($options['p']) && is_array($options['p']) ? (
            array_push($excludeOptions['dirs'], explode(',', implode(',', $options['p'])))
        ) : (
            isset($options['p']) ?
                array_push($excludeOptions['dirs'], explode(',', $options['p'])) :
                'NOP'
            );

        isset($options['exclude-path']) && is_array($options['exclude-path']) ? (
            array_push($excludeOptions['dirs'], explode(',', implode(',', $options['exclude-path'])))
        ) : (
            isset($options['exclude-path']) ?
                array_push($excludeOptions['dirs'], explode(',', $options['exclude-path'])) :
                'NOP'
        );

        isset($options['f']) && is_array($options['f']) ? (
            array_push($excludeOptions['files'], explode(',', implode(',', $options['f'])))
        ) : (
            isset($options['f']) ?
                array_push($excludeOptions['files'], explode(',', $options['f'])) :
                'NOP'
        );

        isset($options['exclude-file']) && is_array($options['exclude-file']) ? (
            array_push($excludeOptions['files'], explode(',', implode(',', $options['exclude-file'])))
        ) : (
            isset($options['exclude-file']) ?
                array_push($excludeOptions['files'], explode(',', $options['exclude-file'])) :
                'NOP'
        );


        isset($excludeOptions['dirs']) && is_array($excludeOptions['dirs']) && count($excludeOptions['dirs']) === 1 ?
            $excludeOptions['dirs'] = $excludeOptions['dirs'][0] :
            'NOP';
        isset($excludeOptions['files']) && is_array($excludeOptions['files']) && count($excludeOptions['files']) === 1 ?
            $excludeOptions['files'] = $excludeOptions['files'][0] :
            'NOP';

        return $excludeOptions;
    }

    /**
     * Checks if a filename ends with /. or /..
     * because this are special unix files
     *
     * @param string $filename
     * @return boolean
     */
    protected function isSpecialDir($fileName)
    {
        return substr($fileName, -2) === '/.' || substr($fileName, -3) === '/..';
    }

    /**
     * Prints the usage
     *
     * @return void
     */
    protected function printUsage()
    {
        printf('Usage:' . PHP_EOL);
        printf('editorconfig-checker [OPTIONS] <FILE>|<FILEGLOB>' . PHP_EOL);
        printf('available options:' . PHP_EOL);
        printf('-a, --auto-fix' . PHP_EOL);
        printf(
            "\twill automatically fix fixable issues(insert_final_newline, end_of_line, trim_trailing_whitespace)"
            . PHP_EOL
        );
        printf('-d, --dotfiles' . PHP_EOL);
        printf("\tuse this flag if you want to also include dotfiles" . PHP_EOL);
        printf('-e <PATTERN>, --exclude <PATTERN>' . PHP_EOL);
        printf("\tstring or regex to filter files which should not be checked" . PHP_EOL);
        printf('-h, --help'. PHP_EOL);
        printf("\twill print this help text" . PHP_EOL);
        printf('-l, --list-files'. PHP_EOL);
        printf("\twill print all files which are checked to stdout" . PHP_EOL);
    }
}
