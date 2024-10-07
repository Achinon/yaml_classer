<?php

namespace Achinon\YamlClasserBundle\Service;

use Achinon\ToolSet\Dumper;
use Achinon\ToolSet\Parser;
use Achinon\YamlClasserBundle\Util\FileHandler;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;
use Exception;

class ChecksumHandler
{
    const checksum_file_path = '../checksum.yml';
    private array $checksumData;

    public function __construct(private FileHandler $fileHandler){
        $this->fileHandler->ensureFileExists($this->getChecksumFilePath());
        $this->checksumData = Yaml::parseFile($this->getChecksumFilePath()) ?? [];
    }

    public function getChecksumFilePath(): string
    {
        return sprintf('%s/%s', __DIR__, self::checksum_file_path);
    }

    public static function dataToChecksum(string|array $data)
    {
        if(is_array($data)){
            $data = Parser::arrayToString($data, ',');
        }
        return md5($data);
    }

    /**
     * @throws Exception
     */
    public function updateChecksum(string $checksum, string $className): void
    {
        $this->checksumData[$className] = $checksum;
    }

    public function saveChecksum()
    {
        $this->fileHandler->saveToFile($this->getChecksumFilePath(), Yaml::dump($this->checksumData));
    }

    public function didChecksumChange(string $checksum, string $generatedClassName)
    {
        if(!isset($this->checksumData[$generatedClassName])){
            return true;
        }
        return $this->checksumData[$generatedClassName] === $checksum;
    }
}
