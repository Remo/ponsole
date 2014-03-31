<?php

namespace Ponsole\Concrete5;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ponsole\CommandInterface;
use Ponsole\BaseCommand;

/**
 * @author Remo Laubacher <remo.laubacher@gmail.com>
 * @package Ponsole
 */
class PackageSinglePageCommand extends BaseCommand implements CommandInterface {

    const PACKAGE_PATH = 'C:\work\club66.ch\packages';

    public function configure() {
        $this
                ->setName('concrete5package:singlepage')
                ->setDescription('helps you to manage your concrete5 site')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        
    }

}
