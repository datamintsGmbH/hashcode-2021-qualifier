<?php

declare(strict_types=1);

namespace Datamints\HashCode\Qualifier2021\Command;

/**
 * Solve problem
 */
class SolveCommand extends \Symfony\Component\Console\Command\Command
{

    const LINES_PER_PROBLEM = 2; // @todo Adjust to problem statement

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('solve')
             ->setDescription('Solve the problem')
             ->addArgument('strategy', \Symfony\Component\Console\Input\InputArgument::REQUIRED, 'Strategy for solving (class name without namespace)')
             ->addArgument('input-file', \Symfony\Component\Console\Input\InputArgument::REQUIRED, 'Input file')
             ->addArgument('output', \Symfony\Component\Console\Input\InputArgument::OPTIONAL, 'Output file');
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
        $inputFileHandle = fopen($inputFile, 'r+');
        if ($inputFileHandle === false) {
            throw new \RuntimeException('Input file could not be opened for reading');
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

        // Read in base data.
        $baseData = $this->readBaseData($inputFileHandle);

        // Check for strategy.
        $strategyClass = 'Datamints\\HashCode\\Qualifier2021\\Strategy\\' . $input->getArgument('strategy');
        if (!class_exists($strategyClass)) {
            throw new \RuntimeException('Strategy could not be found');
        }
        /** @var \Datamints\HashCode\Qualifier2021\Strategy\StrategyInterface $strategy */
        $strategy = new $strategyClass($baseData);

        // Read input file and apply strategy.
        $solutions = [];
        while ($lines = $this->readLines($inputFileHandle)) {
            $outputLines = $strategy->solve($lines);
            $solutions[] = $outputLines;
        }

        // Close input file handle.
        fclose($inputFileHandle);

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

    /**
     * Read base data.
     *
     * @param resource $inputFileHandle File handle to read from
     * @return array
     */
    protected function readBaseData($inputFileHandle): array
    {
        // @todo Read and parse base data
        $line = fgets($inputFileHandle, 4096);

        return explode(' ', $line);
    }

    /**
     * Read problem lines.
     *
     * @param resource $inputFileHandle File handle to read from
     * @return null|array
     */
    protected function readLines($inputFileHandle): ?array
    {
        if (feof($inputFileHandle)) {
            return null;
        }

        $lines = [];
        for ($i = 0; $i < self::LINES_PER_PROBLEM; $i++) {
            if ($line = fgets($inputFileHandle, 4096)) {
                $lines[] = explode(' ', $line);
            }
            else {
                throw new \RuntimeException('Problem could not be read from input file');
            }
        }

        return $lines;
    }

}
