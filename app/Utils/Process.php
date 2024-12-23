<?php

namespace App\Utils;

use Symfony\Component\Process\Process as SymfonyProcess;

class Process extends SymfonyProcess
{
    /**
     * Run the process.
     *
     * @param callable|null $callback
     * @param array $env
     * @return int
     */
    public function run(?callable $callback = null, array $env = []): int
    {
        if (config('app.debug') === false) {
            return parent::run($callback, $env);
        }
        return 0;
    }

    /**
     * Get the output of the process.
     *
     * @param string $debugOutput
     * @return string
     */
    public function getOutput(string $debugOutput = ''): string
    {
        if (config('app.debug') === false) {
            return parent::getOutput();
        }
        return $debugOutput;
    }

    /**
     * Get the exit code of the process.
     *
     * @param string $debugOutput
     * @return string
     */
    public function getExitCode(): ?int
    {
        if (config('app.debug') === false) {
            return parent::getExitCode();
        }
        return 0;
    }
}
