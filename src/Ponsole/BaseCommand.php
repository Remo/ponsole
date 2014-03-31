<?php

namespace Ponsole;

use Symfony\Component\Console\Command\Command;

/**
 * The base command where common methods should be kept
 * 
 * @author Remo Laubacher <remo.laubacher@gmail.com>
 * @package Ponsole
 */
abstract class BaseCommand extends Command {

    protected function copyWithParameters($sourceDirectory, $destinationDirectory, $parameters = array()) {
        $recursiveIteratorSkipDots = new \RecursiveDirectoryIterator($sourceDirectory, \RecursiveDirectoryIterator::SKIP_DOTS);
        foreach ($iterator = new \RecursiveIteratorIterator($recursiveIteratorSkipDots, \RecursiveIteratorIterator::SELF_FIRST) as $item) {
            if ($item->isDir()) {
                mkdir($destinationDirectory . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            } else {
                if (empty($parameters)) {
                    copy($item, $destinationDirectory . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
                } else {
                    $content = file_get_contents($item);
                    foreach ($parameters as $parameterKey => $parameterValue) {
                        $content = str_replace("\${$parameterKey}\$", $parameterValue, $content);
                    }
                    file_put_contents($destinationDirectory . DIRECTORY_SEPARATOR . $iterator->getSubPathName(), $content);
                }
            }
        }
    }

    protected function camelCase($input) {
        $output = preg_replace('#[\s]+#', '', ucwords($input));
        return $output;
    }

}
