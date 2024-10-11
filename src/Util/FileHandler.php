<?php

namespace Achinon\YamlClasserBundle\Util;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Exception;

class FileHandler
{
    public function __construct(private Filesystem $filesystem){}

    public function ensureFileExists(string $filePath, bool $createIfNotExists = true, int $permissions = 0644): void
    {
        if (!$this->filesystem->exists($filePath)) {
            if($createIfNotExists){
                if (!$this->filesystem->exists($directoryPath = dirname($filePath))) {
                    $this->filesystem->mkdir($directoryPath, 0755);
                }
                $this->filesystem->touch($filePath);
                $this->filesystem->chmod($filePath, $permissions);
                return;
            }
            throw new IOException('Provided file does not exist.');
        }
    }

    public function saveClassFile(string $className, string $content, ?string $customFilePath = null): void
    {
        $fileDir = $customFilePath ?? __DIR__."/../Generated";
        $filePath = "$fileDir/$className.php";
        $this->ensureFileExists($filePath);
        $this->filesystem->dumpFile($filePath, $content);
    }

    public function getExtensionFromPath(string $filePath): string
    {
        $bits = explode(".", $filePath);
        return end($bits);
    }

    public function saveToFile(string $filePath, string $yamlFileData): void
    {
        $this->filesystem->dumpFile($filePath, $yamlFileData);
    }
}
