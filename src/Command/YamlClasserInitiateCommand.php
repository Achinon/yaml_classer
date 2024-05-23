<?php

namespace Achinon\YamlClasserBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Achinon\YamlClasserBundle\Generator;

#[AsCommand(name: 'achinon:yaml_classer', description: 'Generates PHP Class file based on your YAML config file.')]
class YamlClasserInitiateCommand extends Command
{
    public function __construct(){
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

        $io = new SymfonyStyle($input, $output);
        $io->title("Generating YAML configuration for file: $file");

        try{
            Generator::initiate($file, $base_class_name);
        }
        catch(\Exception $e){
            $io->error($e->getMessage());
        }
        $io->success(sprintf("File generated. You can now access your config through the class %s in your project.", $base_class_name));

        // Indicate success or failure with the exit code
        return Command::SUCCESS;
    }
}
