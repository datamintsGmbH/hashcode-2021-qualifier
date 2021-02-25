<?php

declare(strict_types=1);

namespace Datamints\HashCode\Qualifier2021\Command;

/**
 * Solve problem
 */
class SolveCommand extends \Symfony\Component\Console\Command\Command
{

    const LINES_PER_PROBLEM = 1; // @todo Adjust to problem statement

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('solve')
             ->setDescription('Solve the problem')
             ->addArgument('strategy', \Symfony\Component\Console\Input\InputArgument::REQUIRED, 'Strategy for solving (class name without namespace)')
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

        // Check for output file.
        $outputFile = $input->getArgument('output-file');
        if (!$outputFile) {
            $outputFile = basename($inputFile) . '.out';
        }
        $outputFileHandle = fopen($outputFile, 'w+');
        if ($outputFileHandle === false) {
            throw new \RuntimeException('Output file could not be opened for writing');
        }

        // Extract base data.
        $baseData = $this->extractBaseData($inputLines);

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

    /**
     * Extract base data.
     *
     * @param array $inputLines Whole set of input lines
     * @return array
     */
    protected function extractBaseData(array $inputLines): array
    {
        // Read base data.
        list($time, $numberCrossings, $numberStreets, $numberCars, $bonus) = array_shift($inputLines);

        // Read streets.
        $streets = [];
        for ($i = 0; $i < $numberStreets; $i++) {
            list($start, $end, $name, $time) = array_shift($inputLines);
            $streets[] = compact('start', 'end', 'name', 'time');
        }

        // Read cars.
        $cars = [];
        for ($i = 0; $i < $numberCars; $i++) {
            $line = array_shift($inputLines);
            $cars[] = [
                'numberStreets' => array_shift($line),
                'streets' => $line,
            ];
        }

        // Build crossings.
        $crossings = [];
        for ($i = 0; $i < $numberCrossings; $i++) {
            $crossings[$i] = [
                'incomingStreets' => [],
                'outgoingStreets' => [],
            ];
        }
        foreach ($streets as $street) {
            $crossings[$street['start']]['outgoingStreets'][] = $street['name'];
            $crossings[$street['end']]['incomingStreets'][] = $street['name'];
        }

        // Combine everything.
        $baseData = compact('time', 'numberCrossings', 'numberStreets', 'numberCars', 'bonus', 'streets', 'cars', 'crossings');

        return $baseData;
    }

}
