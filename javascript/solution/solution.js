const path = require('path');
const fs = require('fs');

// Check arguments.
if (process.argv.length !== 3) {
    console.error('Not enough arguments!');
    process.exit(1);
}

// Get and read problem file.
const problemFile = path.resolve(process.argv[2]);
const problemContents = fs.readFileSync(problemFile).toString().trim();
const problemLines = problemContents.split("\n").map(line => line.split(' '));
const problemInfo = problemLines.shift();

// Compose the solution.
// @todo Implement algorithm according to problem statement

// Write output to file.
const solutionFile = path.resolve(path.basename(problemFile) + '.out');
const output = fs.createWriteStream(solutionFile);
// @todo Write solution in correct format to file
//output.write(solutionLines + "\n");
output.on('finish', () => {
	output.end();
	output.close();
});
