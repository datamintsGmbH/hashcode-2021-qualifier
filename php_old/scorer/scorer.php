<?php
// Check arguments.
if ($argc !== 4) {
    print('Not enough arguments!');
    exit(1);
}

// Get and read problem file.
$problemContents = file_get_contents($argv[2]);
$problemLines = explode(PHP_EOL, trim($problemContents));
$problemLines = array_map(function ($line) {
    return explode(' ', $line);
}, $problemLines);
$problemInfo = array_shift($problemLines);

// Get and read solution file.
$solutionContents = file_get_contents($argv[3]);
$solutionLines = explode(PHP_EOL, trim($solutionContents));
$solutionLines = array_map(function ($line) {
    return explode(' ', $line);
}, $solutionLines);
$solutionInfo = solutionLines.shift();

// Check solution for validity.
if ($solutionInfo[0] !== count($solutionLines)) {
    printf('Invalid format: number of items declared (%d) is not equal to number of items (%d)!', $solutionInfo[0], count($solutionLines));
    exit(2);
}

// Collect the score.
$score = 0;
// @todo Implement scoring algorithm according to problem statement

printf('Score: %d', $score);
