<?php

namespace PDepend;

use BadMethodCallException;
use PDepend\TextUI\Command;
use RuntimeException;

class MockCommand extends Command
{
    public static function main(): int
    {
        $command = new self();

        return $command->run();
    }

    protected function parseArguments(): bool
    {
        return true;
    }

    protected function printVersion(): void
    {
        $cause = $this->getCause();

        throw new RuntimeException('Bad usage', 42, $cause);
    }

    private function getCause(): BadMethodCallException
    {
        return new BadMethodCallException('Cause', 33);
    }
}
