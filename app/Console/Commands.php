<?php

namespace App\Console;

use App\Utils\Process;
use Illuminate\Support\Facades\Log;

/**
 * Collection of exec commands.
 * The commands return values accordingly in deug mode as well.
 */
class Commands
{
    public static function pingRouter($router)
    {
        if (preg_match('/[a-zA-Z]/', $router->ip)) {
            throw new \InvalidArgumentException("Invalid IP address: " . $router->ip);
        }

        $process = Process::fromShellCommandline(config('commands.ping') . " $router->ip -c 1 | grep 'error\|unreachable'");
        $process->run();
        $result = $process->getOutput(debugOutput: rand(1, 10) > 9 ? "error" : '');
        return $result;
    }

    public static function latexToPdf($path, $outputDir)
    {
        $process = new Process([config('commands.pdflatex'), '-interaction=nonstopmode', '-output-dir', $outputDir, $path]);
        $process->run();
        $result = $process->getOutput(debugOutput: 'ok');
        return $result;
    }
}
