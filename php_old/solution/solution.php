<?php
// Check arguments.
if ($argc !== 2) {
    print('Not enough arguments!');
    exit(1);
}

// Get and read problem file.
$problemContents = file_get_contents($argv[1]);
$problemLines = explode(PHP_EOL, trim($problemContents));
$problemLines = array_map(function ($line) {
    return explode(' ', $line);
}, $problemLines);
$problemInfo = array_shift($problemLines);

// Compose the solution.
// @todo Implement algorithm according to problem statement

// Write output to file.
$solutionFile = explode('.', basename(__FILE__))[0].'/'.basename($argv[1]) . '.out';
// @todo Write solution in correct format to file
