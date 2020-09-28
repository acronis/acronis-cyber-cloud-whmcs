<?php
/**
 * @Copyright © 2002-2020 Acronis International GmbH. All rights reserved
 */

namespace Acronis\UsageReport\Service;

use AcronisCloud\Service\Logger\LoggerAwareTrait;

class FileManager
{
    use LoggerAwareTrait;

    /**
     * @param string $path
     */
    public function removeFilesRecursively($path)
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileInfo) {
            $path = $fileInfo->getRealPath();
            $fileInfo->isDir() ? rmdir($path) : unlink($path);
        }
    }

    /**
     * @param string $path
     */
    public function removeEmptyFoldersInPath($path)
    {
        foreach (new \DirectoryIterator($path) as $dirInfo) {
            if (!$dirInfo->isDir() || $dirInfo->isDot()) {
                continue;
            }

            $dirPath = $dirInfo->getRealPath();
            $isDirEmpty = !(new \FilesystemIterator($dirPath))->valid();

            if ($isDirEmpty) {
                $this->getLogger()->notice(
                    'Attempt to remove empty folder "{0}"...',
                    [$dirPath]
                );

                rmdir($dirPath);

                $this->getLogger()->notice(
                    'Removed empty folder "{0}".',
                    [$dirPath]
                );
            } else {
                $this->getLogger()->notice(
                    'Non-empty folder "{0}" removal attempted and not completed.',
                    [$dirPath]
                );
            }
        }
    }
}