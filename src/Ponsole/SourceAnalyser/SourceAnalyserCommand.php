<?php

namespace Ponsole\SourceAnalyser;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ponsole\CommandInterface;
use Ponsole\BaseCommand;

class SourceAnalyserCommand extends BaseCommand implements CommandInterface {

    public function configure() {
        $this
                ->setName('source:analyse')
                ->addArgument('dir', InputArgument::OPTIONAL, 'The directory to be analysed')
                ->addOption('ignore-comments', null, InputOption::VALUE_NONE)
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $dir = realpath($input->getArgument('dir'));

        $directoryIterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
        $regexIterator = new \RegexIterator($directoryIterator, '/^.+\.php$/i', \RecursiveRegexIterator::GET_MATCH);

        $totalLineCount = 0;
        $totalCommentCount = 0;
        $fileCount = 0;
        
        foreach ($regexIterator as $fileInfo) {
            $fileName = $fileInfo[0];

            $fileContent = file_get_contents($fileName);
            $fileLineCount = substr_count($fileContent, "\n");
            $fileCommentCount = 0;

            foreach (token_get_all($fileContent) as $index => $token) {
                if (is_string($token)) {
                    continue;
                }

                list ($token, $value) = $token;

                if ($token == T_COMMENT || $token == T_DOC_COMMENT) {
                    $fileCommentCount += substr_count($value, "\n") + 1;
                }
            }
            
            $totalLineCount += $fileLineCount;
            $totalCommentCount += $fileCommentCount;
            $fileCount++;
        }

        $output->writeln(sprintf("\n<info>We've analysed %s and found:\n"
                . "\n"
                . "Files: %d\n"
                . "Lines of code: %d\n"
                . "Lines of comments: %d\n"
                . "</info>", 
                $dir, $fileCount, $totalLineCount - $totalCommentCount, $totalCommentCount));
    }

}
