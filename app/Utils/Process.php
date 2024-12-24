<?php

namespace App\Utils;

use Symfony\Component\Process\Process as SymfonyProcess;

class Process extends SymfonyProcess
{
    /**
     * Run the process. If the app is in debug mode, the process will not be executed.
     *
     * @param callable|null $callback
     * @param array $env
     * @return int
     */
    public function run(?callable $callback = null, array $env = []): int
    {
        if (config('app.debug') === false || config('commands.run_in_debug') === true) {
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
        if (config('app.debug') === false || config('commands.run_in_debug') === true) {
            return parent::getOutput();
        }
        return $debugOutput;
    }

    /**
     * Get the exit code of the process.
     *
     * @param string $debugOutput
     * @return int|null
     */
    public function getExitCode(): ?int
    {
        if (config('app.debug') === false || config('commands.run_in_debug') === true) {
            return parent::getExitCode();
        }
        return 0;
    }
}
