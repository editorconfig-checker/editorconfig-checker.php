<?php

namespace EditorconfigChecker;

class Utilities
{
    /**
     * Returns the architecture of the current machine
     */
    public static function getCurrentArch(): string
    {
        $arch = php_uname('m');

        switch ($arch) {
            case 'x86_64':
            case 'AMD64':
                return "amd64";
            case 'i386':
                return "386";
            case 'arm64':
            case 'aarch64':
                return "arm64";
            default:
                printf('ERROR: Unexpected, please contact the maintainer or provide a pull request :)%s', PHP_EOL);
                exit(1);
        }
    }

    /**
     * Returns the operating system of the current machine
     */
    public static function getCurrentOs(): string
    {
        $os = strtolower(php_uname('s'));
        if ($os === 'windows nt') {
            $os = 'windows';
        }

        return $os;
    }

    /**
     * Returns the releasename needed for this machine
     */
    public static function getReleaseName(): string
    {
        return sprintf('ec-%s-%s', Utilities::getCurrentOs(), Utilities::getCurrentArch());
    }

    /**
     * Returns the name of the release archive published by the core
     */
    public static function getArchiveName(): string
    {
        $os = Utilities::getCurrentOs();
        // the core ships a single universal binary for macOS
        $arch = $os === 'darwin' ? 'all' : Utilities::getCurrentArch();

        return sprintf('editorconfig-checker-%s-%s', $os, $arch);
    }

    /**
     * Returns the name of the binary inside the release archive
     */
    public static function getArchivedBinaryName(): string
    {
        $binaryName = 'editorconfig-checker';
        if (self::getCurrentOs() === 'windows') {
            $binaryName .= '.exe';
        }

        return $binaryName;
    }

    /**
     * Returns the root path of this library
     */
    public static function getBasePath(): string
    {
        $basePath = sprintf("%s/../..", dirname(__FILE__));
        return $basePath;
    }


    /**
     * returns the binary name
     */
    public static function getBinaryPath(): string
    {
        $binaryName = Utilities::getReleaseName();
        if (self::getCurrentOs() === 'windows') {
            $binaryName .= '.exe';
        }
        $binaryPath = sprintf('%s/bin/%s', Utilities::getBasePath(), $binaryName);

        return $binaryPath;
    }

    /**
     * Downloads the release from the release page
     */
    public static function downloadReleaseArchive(string $releaseName, string $version): bool
    {
        $archivePath = sprintf('%s/%s.tar.gz', Utilities::getBasePath(), $releaseName);

        $releaseUrl = sprintf(
            'https://github.com/editorconfig-checker/editorconfig-checker/releases/download/v%s/%s.tar.gz',
            $version,
            Utilities::getArchiveName()
        );

        $result = file_put_contents($archivePath, fopen($releaseUrl, 'r'));
        return $result > 0;
    }

    /**
     * decompresses and extracts the release archive
     */
    public static function extractReleaseArchive(string $releaseName): bool
    {
        return Utilities::decompress($releaseName) && Utilities::unpack($releaseName);
    }

    /**
     * decompresses the release archive
     */
    public static function decompress(string $releaseName): bool
    {
        try {
            $p = new \PharData(sprintf("%s/%s.tar.gz", Utilities::getBasePath(), $releaseName));
            $p->decompress();
        } catch (\Exception $e) {
            printf('ERROR: Can not decompress the archive%s%s', PHP_EOL, $e);
            return false;
        }

        return true;
    }

    /**
     * unpacks the release archive
     */
    public static function unpack(string $releaseName): bool
    {
        try {
            $archivedBinaryName = Utilities::getArchivedBinaryName();
            $p = new \PharData(sprintf("%s/%s.tar", Utilities::getBasePath(), $releaseName));
            $p->extractTo(Utilities::getBasePath(), $archivedBinaryName, true);

            $extractedBinaryPath = sprintf("%s/%s", Utilities::getBasePath(), $archivedBinaryName);
            if (!rename($extractedBinaryPath, Utilities::getBinaryPath())) {
                printf('ERROR: Can not move the extracted binary%s', PHP_EOL);
                return false;
            }

            if (!unlink(sprintf("%s/%s.tar", Utilities::getBasePath(), $releaseName))) {
                printf('ERROR: Can not remove the decompressed archive%s', PHP_EOL);
                return false;
            }
        } catch (\PharException $e) {
            printf('ERROR: Can not unpack the archive%s%s', PHP_EOL, $e);
            return false;
        }

        return true;
    }

    /**
     * Constructs the arguments the binary needs to be called by
     * the arguments providedunline
     *
     * @param string[] $arguments
     */
    public static function constructStringFromArguments(array $arguments): string
    {
        $result = '';
        foreach ($arguments as $argument) {
            $result .= ' ' . $argument;
        }

        return $result;
    }

    /**
     * Removes all intermediate files
     */
    public static function cleanup(): void
    {
        $releaseName = sprintf("%s/%s", Utilities::getBasePath(), Utilities::getReleaseName());
        if (is_file($releaseName . '.tar.gz')) {
            unlink($releaseName . '.tar.gz');
        }

        if (is_file($releaseName . '.tar')) {
            unlink($releaseName . '.tar');
        }

        $extractedBinary = sprintf("%s/%s", Utilities::getBasePath(), Utilities::getArchivedBinaryName());
        if (is_file($extractedBinary)) {
            unlink($extractedBinary);
        }
    }
}
