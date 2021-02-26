<?php

declare(strict_types=1);

namespace Datamints\HashCode\Qualifier2021\Command;

/**
 * Solve problem
 */
class SolveCommand extends \Symfony\Component\Console\Command\Command
{

    use \Datamints\HashCode\Qualifier2021\Traits\BaseDataTrait;

    const LINES_PER_PROBLEM = 1; // @todo Adjust to problem statement

    const FORMAT_TXT = 'text';
    const FORMAT_SERIALIZED = 'serialized';

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('solve')
             ->setDescription('Solve the problem')
             ->addArgument('strategy', \Symfony\Component\Console\Input\InputArgument::REQUIRED, 'Strategy for solving (class name without namespace)')
             ->addArgument('input-file', \Symfony\Component\Console\Input\InputArgument::REQUIRED, 'Input file')
             ->addArgument('output-file', \Symfony\Component\Console\Input\InputArgument::OPTIONAL, 'Output file')
            ->addOption('format', null, \Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'Input format', self::FORMAT_TXT);
    }

    /**
     * @inheritDoc
     */
    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        // Remember start time.
        $start = microtime(true);

        // Check for input file.
        $inputFile = $input->getArgument('input-file');
        if (!is_file($inputFile)) {
            throw new \RuntimeException('Input file could not be found');
        }

        // Read input file.
        $inputData = file_get_contents($inputFile);
        if ($inputData === false) {
            throw new \RuntimeException('Input file could not be opened for reading');
        }

        // Process depending on format.
        switch ($input->getOption('format')) {
            case self::FORMAT_SERIALIZED:
                $baseData = unserialize($inputData);
                if ($baseData === false) {
                    throw new \RuntimeException('Input data is not serialized');
                }
                break;

            case self::FORMAT_TXT:
            default:
                // Split into lines and lines into parts.
                $inputLines = explode(PHP_EOL, $inputData);
                $inputLines = array_map(function (string $line): array {
                    return explode(' ', trim($line));
                }, $inputLines);

                // Extract base data.
                $baseData = $this->extractBaseData($inputLines);
        }

        // Check for output file.
        $outputFile = $input->getArgument('output-file');
        if (!$outputFile) {
            $outputFile = basename($inputFile) . '.out';
        }
        $outputFileHandle = fopen($outputFile, 'w+');
        if ($outputFileHandle === false) {
            throw new \RuntimeException('Output file could not be opened for writing');
        }

        // Check for strategy.
        $strategyClass = 'Datamints\\HashCode\\Qualifier2021\\Strategy\\' . $input->getArgument('strategy');
        if (!class_exists($strategyClass)) {
            throw new \RuntimeException('Strategy could not be found');
        }
        /** @var \Datamints\HashCode\Qualifier2021\Strategy\StrategyInterface $strategy */
        $strategy = new $strategyClass($baseData);

        // Solve the problem.
        $solutions = $strategy->solve();

        // Write output.
        fwrite($outputFileHandle, count($solutions) . PHP_EOL);
        foreach ($solutions as $solution) {
            foreach ($solution as $line) {
                fwrite($outputFileHandle, implode(' ', $line) . PHP_EOL);
            }
        }

        // Close output file handle.
        fclose($outputFileHandle);

        // Write stats.
        $output->writeln(sprintf('Runtime: %0.6f s', microtime(true) - $start));

        return 0;
    }

}
