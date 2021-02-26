<?php

declare(strict_types=1);

namespace Datamints\HashCode\Qualifier2021\Command;

/**
 * Serialize base data for later use
 */
class SerializeCommand extends \Symfony\Component\Console\Command\Command
{

    use \Datamints\HashCode\Qualifier2021\Traits\BaseDataTrait;

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('serialize')
             ->setDescription('Serialize base data for later use')
             ->addArgument('input-file', \Symfony\Component\Console\Input\InputArgument::REQUIRED, 'Input file')
             ->addArgument('output-file', \Symfony\Component\Console\Input\InputArgument::OPTIONAL, 'Output file');
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

        // Split into lines and lines into parts.
        $inputLines = explode(PHP_EOL, $inputData);
        $inputLines = array_map(function (string $line): array {
            return explode(' ', trim($line));
        }, $inputLines);

        // Extract base data.
        $baseData = $this->extractBaseData($inputLines);

        // Check for output file.
        $outputFile = $input->getArgument('output-file');
        if (!$outputFile) {
            $outputFile = basename($inputFile) . '.serialized';
        }

        // Write data to file.
        file_put_contents($outputFile, serialize($baseData));

        // Write stats.
        $output->writeln(sprintf('Runtime: %0.6f s', microtime(true) - $start));

        return 0;
    }

}
