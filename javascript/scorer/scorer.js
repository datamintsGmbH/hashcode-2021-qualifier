const path = require('path');
const fs = require('fs');

// Check arguments.
if (process.argv.length !== 4) {
    console.error('Not enough arguments!');
    process.exit(1);
}

// Get and read problem file.
const problemFile = path.resolve(process.argv[2]);
const problemContents = fs.readFileSync(problemFile).toString().trim();
const problemLines = problemContents.split("\n").map(line => line.split(' '));
const problemInfo = problemLines.shift();

// Get and read solution file.
const solutionFile = path.resolve(process.argv[3]);
const solutionContents = fs.readFileSync(solutionFile).toString().trim();
const solutionLines = solutionContents.split("\n").map(line => line.split(' '));
const solutionInfo = solutionLines.shift();

// Check solution for validity.
if (parseInt(solutionInfo[0]) !== solutionLines.length) {
    console.error(`Invalid format: number of items declared (${solutionInfo[0]}) is not equal to number of items (${solutionLines.length})!`);
    process.exit(2);
}

// Collect the score.
let score = 0;
// @todo Implement scoring algorithm according to problem statement

console.log('Score: ' + score);
