<?php

namespace PDepend;

use BadMethodCallException;
use PDepend\TextUI\Command;
use RuntimeException;

class MockCommand extends Command
{
    public static function main()
    {
        $command = new self();

        return $command->run();
    }

    protected function parseArguments(): void
    {
    }

    protected function printVersion(): void
    {
        $cause = $this->getCause();

        throw new RuntimeException('Bad usage', 42, $cause);
    }

    private function getCause()
    {
        return new BadMethodCallException('Cause', 33);
    }
}
