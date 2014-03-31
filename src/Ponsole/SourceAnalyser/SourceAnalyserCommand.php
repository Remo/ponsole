<?php

namespace Ponsole\SourceAnalyser;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ponsole\CommandInterface;
use Ponsole\BaseCommand;

/**
 * Command to analyse source code
 * 
 * @author Remo Laubacher <remo.laubacher@gmail.com>
 */
class SourceAnalyserCommand extends BaseCommand implements CommandInterface {

    public function configure() {
        $this
                ->setName('source:analyse')
                ->addArgument('dir', InputArgument::OPTIONAL, 'The directory to be analysed')
                ->addOption('ignore-comments', null, InputOption::VALUE_NONE)
        ;
    }

    /**
     * Analyses a single file specified by its name with the parameter $fileName
     * Returns an array with two names entries:
     * <code>
     * array(
     *   'lineCount' => 212,
     *   'commentLineCount' => 56
     * );
     * </code>
     * 
     * @param string $fileName
     * @return array
     */
    protected function analyseFile($fileName) {
        $fileContent = file_get_contents($fileName);
        $fileLineCount = substr_count($fileContent, "\n");
        $fileCommentLineCount = 0;

        foreach (token_get_all($fileContent) as $index => $token) {
            if (is_string($token)) {
                continue;
            }

            list ($token, $value) = $token;

            if ($token == T_COMMENT || $token == T_DOC_COMMENT) {
                $fileCommentLineCount += substr_count($value, "\n") + 1;
            }
        }

        return ['lineCount' => $fileLineCount, 'commentLineCount' => $fileCommentLineCount];
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $dir = realpath($input->getArgument('dir'));

        $directoryIterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
        $regexIterator = new \RegexIterator($directoryIterator, '/^.+\.php$/i', \RecursiveRegexIterator::GET_MATCH);

        $totalLineCount = 0;
        $totalCommentCount = 0;
        $fileCount = iterator_count($regexIterator);

        // start progress bar
        $progress = $this->getHelperSet()->get('progress');
        $progress->start($output, $fileCount);

        // start scanning files
        foreach ($regexIterator as $fileInfo) {
            // analyse file
            $fileName = $fileInfo[0];

            $codeInfo = $this->analyseFile($fileName);

            $totalLineCount += $codeInfo['lineCount'];
            $totalCommentCount += $codeInfo['commentLineCount'];

            // advance the progress bar 1 unit
            $progress->advance();
        }

        // progress finished
        $progress->finish();

        // print result
        $output->writeln(sprintf("\n<info>We've analysed %s and found:\n"
                        . "\n"
                        . "Files: %d\n"
                        . "Lines of code: %d\n"
                        . "Lines of comments: %d\n"
                        . "</info>", $dir, $fileCount, $totalLineCount - $totalCommentCount, $totalCommentCount));
    }

}
