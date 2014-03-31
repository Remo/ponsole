<?php

namespace Ponsole;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface CommandInterface {

    function configure();

    function execute(InputInterface $input, OutputInterface $output);
}