<?php

declare(strict_types=1);

namespace Datamints\HashCode\Qualifier2021;

/**
 * The CLI application class
 */
class Application extends \Symfony\Component\Console\Application
{

    /**
     * @inheritDoc
     */
    protected function getDefaultCommands()
    {
        // Get base commands from the parent class.
        $baseCommands = parent::getDefaultCommands();

        // Add our own commands.
        $baseCommands[] = new \Datamints\HashCode\Qualifier2021\Command\ScoreCommand();
        $baseCommands[] = new \Datamints\HashCode\Qualifier2021\Command\SolveCommand();

        return $baseCommands;
    }

}
