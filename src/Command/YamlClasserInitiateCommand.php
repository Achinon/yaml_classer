<?php

namespace Achinon\YamlClasserBundle\Command;

use Achinon\YamlClasserBundle\Service\YamlToPhpConfigFileToClassGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Achinon\YamlClasserBundle\Generator;
use Symfony\Component\Yaml\Yaml;
use Exception;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

#[AsCommand(name: 'achinon:yaml_classer', description: 'Generates PHP Class file based on your YAML config file.')]
class YamlClasserInitiateCommand extends Command
{
    private ?SymfonyStyle $io = null;

    public function __construct(private YamlToPhpConfigFileToClassGenerator $generator)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->addArgument('yaml_file_path', InputArgument::REQUIRED, 'The YAML file to be generated.')
             ->addArgument('class_name', InputArgument::REQUIRED, 'Class you wish to call to access this yaml file.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('yaml_file_path');
        $base_class_name = $input->getArgument('class_name');

        $this->io = $io = new SymfonyStyle($input, $output);
        $io->title("Generating YAML configuration for file: $file");


        try {
            $success = $this->generator->setYamlConfigFilePath($file)->generateConfigFile($base_class_name);
        }
        catch(Exception $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }

        $success ?
            $io->success(sprintf("File generated. You can now access your config through the class %s in your project.", $base_class_name)):
            $io->success("No change found. Skipping generation.");

        return Command::SUCCESS;
    }

    private function addToServices(string $FQCN)
    {
        $serviceFileLocation = __DIR__."/../services.yml";
        $filesystem = $this->filesystem;

        if (!$filesystem->exists($serviceFileLocation)) {
            throw new FileNotFoundException('Service file not found!');
        }

        $existing_services = $new_services = Yaml::parseFile($serviceFileLocation);

        if(isset($existing_services['services'][$FQCN])) {
            return;
        }

        $new_services['services'] = array_merge($existing_services['services'], [$FQCN => ['autowire' => true]]);

        $filesystem->dumpFile($serviceFileLocation, Yaml::dump($new_services, 4));
    }
}