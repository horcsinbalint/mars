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
        // This happens too often to log.
        $process = Process::fromShellCommandline("ping $router->ip -c 1 | grep 'error\|unreachable'");
        $process->run();
        $result = $process->getOutput(debugOutput: rand(1, 10) > 9 ? "error" : '');
        return $result;
    }

    public static function latexToPdf($path, $outputDir)
    {
        $process = new Process(['pdflatex', '-interaction=nonstopmode', '-output-dir', $outputDir, $path]);
        $process->run();
        $result = $process->getOutput(debugOutput: 'ok');
        return $result;
    }
}
