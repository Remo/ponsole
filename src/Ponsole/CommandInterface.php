<?php

namespace Ponsole;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Defines all mandatory methods for a ponsole command
 * 
 * @author Remo Laubacher <remo.laubacher@gmail.com>
 * @package Ponsole
 */
interface CommandInterface {

    /**
     * Use this method to register and configure your command.
     */
    function configure();

    /**
     * This method is called whenever a command is executed. Use the two
     * available parameters to get access to input values and print messages
     * to the output.     
     * 
     * The following examples gets the values of the input parameter with the
     * name "dir"
     * 
     * <code>
     * $dir = $input->getArgument('dir');
     * </code>
     * 
     * This examples print "Hello Command!" to the standard output.
     * <code>
     * $output->writeln('Hello Command!');
     * </code>
     * 
     */
    function execute(InputInterface $input, OutputInterface $output);
}
