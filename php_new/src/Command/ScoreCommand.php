<?php

declare(strict_types=1);

namespace Datamints\HashCode\Qualifier2021\Command;

/**
 * Score solution
 */
class ScoreCommand extends \Symfony\Component\Console\Command\Command
{

    const LINES_PER_SOLUTION = 2; // @todo Adjust to problem statement

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('score')
             ->setDescription('Score a solution')
             ->addArgument('input-file', \Symfony\Component\Console\Input\InputArgument::REQUIRED, 'Input file');
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

        // Read away solution count (first line).
        $solutionCount = intval(array_shift($inputLines));

        // Group remaining lines into solutions.
        $solutions = array_chunk($inputLines, self::LINES_PER_SOLUTION);

        // Read input file and calculate score.
        $scores = array_map(function (array $solution): int {
            return $this->calculateScore($solution);
        }, $solutions);

        // Check for validity.
        if (count($scores) !== $solutionCount) {
            throw new \RuntimeException(sprintf('File is invalid, %d solutions specified, but %d found', $solutionCount, count($scores)));
        }

        // Sum up score.
        $score = array_reduce($scores, function (int $total, int $score): int {
            return $total + $score;
        }, 0);
        $output->writeln(sprintf('Score for input file "%s":', $inputFile));
        $table = new \Symfony\Component\Console\Helper\Table($output);
        $table->addRows([
            [
                'Solutions',
                $solutionCount,
            ],
            [
                'Score',
                $score,
            ],
        ]);
        $table->render();

        // Write stats.
        $output->writeln(sprintf('Runtime: %0.6f s', microtime(true) - $start));

        return 0;
    }

    /**
     * Read solution lines.
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
        for ($i = 0; $i < self::LINES_PER_SOLUTION; $i++) {
            if (($line = fgets($inputFileHandle, 4096)) !== false) {
                if (strlen(trim($line)) === 0) {
                    return null;
                }

                $lines[] = explode(' ', trim($line));
            }
            else {
                throw new \RuntimeException('Solution could not be read from input file');
            }
        }

        return $lines;
    }

    /**
     * Calculate score.
     *
     * @param array $solution Solution (array of lines)
     * @return int
     */
    protected function calculateScore(array $solution): int
    {
        // @todo Add implementation

        return 0;
    }

}
