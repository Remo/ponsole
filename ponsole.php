<?php

require_once('vendor/autoload.php');

use Symfony\Component\Console\Application;

$console = new Application();

// add all active commands
$activeCommands = require 'commands.php';
array_map(function ($command) use ($console) {
    $console->add(new $command);
}, $activeCommands);

$console->run();

