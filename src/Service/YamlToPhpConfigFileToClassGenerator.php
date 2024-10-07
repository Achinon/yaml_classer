<?php

namespace Achinon\YamlClasserBundle\Service;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Yaml\Yaml;

class YamlToPhpConfigFileToClassGenerator extends ConfigFileToClassGenerator
{
    const ALLOWED_EXTENSIONS = ['yaml', 'yml'];

    public function setYamlConfigFilePath (string $yamlFilePath): static
    {
        $extension = $this->fileHandler->getExtensionFromPath($yamlFilePath);

        if(!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            $this->data = null;
        }
        $this->fileHandler->ensureFileExists($yamlFilePath);

        return $this->setData(Yaml::parseFile($yamlFilePath));
    }
}