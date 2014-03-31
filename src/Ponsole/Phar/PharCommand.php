<?php

namespace Ponsole\Phar;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ponsole\CommandInterface;
use Ponsole\BaseCommand;

class PharCommand extends BaseCommand implements CommandInterface {

    public function configure() {
        $this
                ->setName('phar:create')
                ->addArgument('name', InputArgument::REQUIRED, 'The name of the phar file')
                ->addArgument('dir', InputArgument::REQUIRED, 'The directory to be packed')
                ->addArgument('stub', InputArgument::OPTIONAL, 'The stub of your phar archive')
                ->addArgument('webstub', InputArgument::OPTIONAL, 'The stub of your phar archive for webprojects')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $dir = realpath($input->getArgument('dir'));
        $name = $input->getArgument('name');
        $stub = $input->getArgument('stub');
        $webstub = $input->getArgument('webstub');
        
        $phar = new \Phar($name, 0, $name);
        $phar->buildFromDirectory($dir);
        $phar->setStub($phar->createDefaultStub($stub, $webstub));
    }

}
