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

        if ($arch === "x86_64") {
            $arch = "amd64";
        } elseif ($arch === "i386") {
            $arch = "386";
        } else {
            // TODO: Whats here?
            printf('ERROR: Unexpected, please contact the maintainer or provide a pull request :)%s', PHP_EOL);
            exit(1);
        }

        return $arch;
    }

    /**
     * Returns the operating system of the current machine
     */
    public static function getCurrentOs(): string
    {
        $os = strtolower(php_uname('s'));
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
        $binaryPath = sprintf('%s/bin/%s', Utilities::getBasePath(), $binaryName);

        return $binaryPath;
    }

    /**
     * Downloads the release from the release page
     */
    public static function downloadReleaseArchive(string $releaseName, string $version): bool
    {
        $archiveName = $releaseName . '.tar.gz';
        $archivePath = sprintf('%s/%s', Utilities::getBasePath(), $archiveName);
        $releaseUrl = sprintf(
            'https://github.com/editorconfig-checker/editorconfig-checker/releases/download/%s/%s',
            $version,
            $archiveName
        );

        $result = file_put_contents($archivePath, fopen($releaseUrl, 'r'));
        return $result > 0 || $result;
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
        } catch (Exception $e) {
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
            $p = new \PharData(sprintf("%s/%s.tar", Utilities::getBasePath(), $releaseName));
            $p->extractTo(Utilities::getBasePath());

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
    }
}
