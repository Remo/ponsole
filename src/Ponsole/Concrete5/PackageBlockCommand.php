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
class PackageBlockCommand extends BaseCommand implements CommandInterface {

    const PACKAGE_PATH = 'C:\work\club66.ch\packages';

    public function configure() {
        $this
                ->setName('concrete5package:block')
                ->setDescription('helps you to manage your concrete5 site')
                ->addArgument('package', InputArgument::REQUIRED, 'The package handle you wish to work with')
                ->addArgument('block', InputArgument::REQUIRED, 'The handle of the block you want to manage')
        ;
    }

    protected function getPackageController($packageHandle) {
        return self::PACKAGE_PATH . DIRECTORY_SEPARATOR . $packageHandle . DIRECTORY_SEPARATOR . 'controller.php';
    }

    protected function copyBlockTemplate($packageHandle, $blockHandle, $parameters) {
        $sourceDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'block';
        $targetDirectory = self::PACKAGE_PATH . DIRECTORY_SEPARATOR . $packageHandle . DIRECTORY_SEPARATOR . 'blocks' . DIRECTORY_SEPARATOR . $blockHandle;

        if (!file_exists($targetDirectory)) {
            mkdir($targetDirectory, 0777, true);
        }

        $this->copyWithParameters(
                $sourceDirectory, $targetDirectory, $parameters
        );
    }

    protected function addBlock($packageHandle, $blockHandle) {
        $result = '';
        $packageController = $this->getPackageController($packageHandle);
        $phpCode = file_get_contents($packageController);
        $tokens = token_get_all($phpCode);
        $numTokens = count($tokens);

        $functionFound = false;
        $installFunctionFound = false;
        $installFunctionBracketFound = false;
        $openBrackets = 0;
        $openFunctionBrackets = 0;

        for ($i = 0; $i < $numTokens; $i++) {
            $token = $tokens[$i];

            if (is_array($token)) {
                switch ($token[0]) {
                    case T_FUNCTION:
                        $functionFound = true;
                        $openFunctionBrackets = $openBrackets;
                        break;
                    case T_WHITESPACE:
                        break;
                    case T_STRING:
                        if ($functionFound && $token[1] === 'install') {
                            $installFunctionFound = true;
                        }
                    default:
                        $functionFound = false;
                }
            } else {
                if ($token === '{') {
                    if ($installFunctionFound) {
                        $installFunctionBracketFound = true;
                    }
                    $openBrackets++;
                }
                if ($token === '}') {
                    $openBrackets--;
                }
            }


            // we've found the end of the install function
            if ($installFunctionBracketFound && $openBrackets == $openFunctionBrackets) {
                $result .= '$btHandle = \'' . $blockHandle . '\';
                    $bt = BlockType::getByHandle($btHandle);
                    if (!is_object($bt)) {
                        BlockType::installBlockTypeFromPackage($btHandle, Package::getByHandle($this->pkgHandle));
                    }' . PHP_EOL;
                $installFunctionFound = false;
                $installFunctionBracketFound = false;
            }

            if (is_array($token)) {
                switch ($token[0]) {
                    default:
                        $result .= $token[1];
                        break;
                }
            } else {
                $result .= $token;
            }
        }

        file_put_contents($packageController, $result);
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $packageHandle = strtolower($input->getArgument('package'));
        $blockHandle = strtolower($input->getArgument('block'));

        $parameters = array(
            'BLOCK_TABLE' => 'bt' . $this->camelCase($blockHandle),
            'BLOCK_CLASS' => $this->camelCase($blockHandle),
        );

        $this->copyBlockTemplate($packageHandle, $blockHandle, $parameters);
        $this->addBlock($packageHandle, $blockHandle);
    }

}
