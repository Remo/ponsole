<?php

use Symfony\Component\Console\Application;

$autoLoader = 'vendor/autoload.php';
if (file_exists($autoLoader)) {
    require_once($autoLoader);
} else {
    echo 'You must install the project dependencies first:' . PHP_EOL .
    'curl -sS https://getcomposer.org/installer | php' . PHP_EOL .
    'php composer.phar install' . PHP_EOL;
    die();
}


// mke sure we're running in CLI mode
if (PHP_SAPI !== 'cli') {
    echo sprintf('Ponsole must be invoked via the CLI version of PHP, not the %s SAPI', PHP_SAPI);
    die();
}

$console = new Application();

// add all active commands
$activeCommands = require 'commands.php';
array_map(function ($command) use ($console) {
    $console->add(new $command);
}, $activeCommands);

$console->run();

