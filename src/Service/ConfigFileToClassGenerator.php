<?php

namespace Achinon\YamlClasserBundle\Service;

use Achinon\ToolSet\Dumper;
use Achinon\YamlClasserBundle\PhpCode\PhpCodeBuilder;
use Achinon\YamlClasserBundle\Util\FileHandler;
use Error;

abstract class ConfigFileToClassGenerator
{
    protected ?string $checksum = null;
    protected ?string $generatedClassName = null;
    protected ?iterable $data = null;

    public function __construct(protected readonly FileHandler $fileHandler,
                                private readonly ChecksumHandler $checksumHandler){}

    // class is set abstract, as this method is supposed to be called only by an extending class
    protected function setData(?iterable $data): static
    {
        if(is_null($data)){
            throw new \Error('Config data could not be read or is empty.');
        }
        $this->data = $data;
        $this->checksum = $this->checksumHandler::dataToChecksum($data);
        return $this;
    }

    protected function getData(): ?iterable
    {
        return $this->data;
    }

    protected function getChecksum(): ?string
    {
        return $this->checksum;
    }

    protected function didChecksumChange(): bool
    {
        return $this->checksumHandler
            ->didChecksumChange($this->getChecksum(),
                                $this->generatedClassName ?? throw new \Exception('Method called at wrong place.'));
    }

    /**
     * @throws \Exception
     */
    public function generateConfigFile(string $className, ?string $customFilePath = null, ?string $customNameSpace = null, bool $forceUpdate = false): bool
    {
        if(is_null($customFilePath) !== is_null($customNameSpace)){
            throw new \Exception('When setting custom namespace or filepath, both must be defined.');
        }

        $this->generatedClassName = $className;

        if (is_null($this->data)) {
            throw new Error('Data is empty. Cannot generate config file.');
        }

        if (!$this->didChecksumChange() && !$forceUpdate) {
            return false;
        }
        $this->checksumHandler->updateChecksum($this->checksumHandler::dataToChecksum($this->data), $className);
        $data = $this->getData();
        $generatedCode = PhpCodeBuilder::buildClassTree($data, $className);
        $generatedCode = PhpCodeBuilder::buildFileIntro($customNameSpace) . PHP_EOL . $generatedCode;

        $this->fileHandler->saveClassFile($className, $generatedCode, $customFilePath);
        $this->checksumHandler->saveChecksum();

        return true;
    }
}
