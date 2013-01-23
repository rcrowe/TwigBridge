<?php

/**
 * Brings Twig to Laravel.
 *
 * @author Rob Crowe <hello@vivalacrowe.com>
 * @license MIT
 */

namespace TwigBridge\Console;

use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;
use TwigBridge\TwigBridge;
use Twig_Error;

/**
 * Lint check Twig templates.
 *
 * Taken from the Symfony TwigBundle:
 * https://github.com/symfony/TwigBundle/blob/master/Command/LintCommand.php
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class LintCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'twig:lint';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lints Twig templates';

    /**
     * @var Twig_Environment
     */
    protected $bridge;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $finder     = new Finder();
        $bridge     = new TwigBridge($this->laravel);
        $this->twig = $bridge->getTwig();
        $paths      = $this->twig->getLoader()->getPaths();

        // Lint check all twig files on our path
        $finder->files()->in($paths)->name('*.'.$bridge->getExtension());

        $pass = 0;
        $fail = 0;

        foreach ($finder as $file) {
            if ($this->processFile($file->getRealPath())) {
                $pass++;
            } else {
                $fail++;
            }
        }

        // Output totals
        if ($fail > 0) {
            $this->line('');
        }

        if ($pass > 0) {
            $msg = ($pass > 1) ? 'files' : 'file';
            $msg = $pass.' '.$msg.' successfully checked';
            $this->info($msg);
        }

        if ($fail > 0) {
            $msg = ($fail > 1) ? 'files' : 'file';
            $msg = $fail.' '.$msg.' failed';
            $this->error($msg);
        }

        // Return exit code
        return ($fail === 0) ? 0 : 1;
    }

    /**
     * Lint check specific file.
     *
     * @param string $file Path to file.
     * @return bool Did the file pass syntax check.
     */
    protected function processFile($file)
    {
        $template = file_get_contents($file);
        $basename = pathinfo($file, PATHINFO_BASENAME);

        try {
            $this->twig->parse($this->twig->tokenize($template, $basename));
            return true;
        } catch (Twig_Error $e) {
            $this->renderException($e, $template, $file);
            return false;
        }
    }

    /**
     * Output to console the failure.
     *
     * @param Twig_Error $exception Exception raised from failed parsing.
     * @param string     $template  Contents of Twig template.
     * @param string     $file      File name.
     * @param void
     */
    protected function renderException(Twig_Error $exception, $template, $file)
    {
        $line  = $exception->getTemplateLine();
        $lines = $this->getContext($template, $line);

        $this->line(sprintf('<error>Fail</error> in %s (line %s)', $file, $line));

        foreach ($lines as $no => $code) {
            $this->line(
                sprintf(
                    "%s %-6s %s",
                    $no == $line ? '<error>>></error>' : '  ',
                    $no,
                    $code
                )
            );

            if ($no == $line) {
                $this->line(sprintf('<error>>> %s</error> ', $exception->getRawMessage()));
            }
        }
    }

    /**
     * Grabs the surrounding lines around the exception.
     *
     * @param string     $template Contents of Twig template.
     * @param string|int $line     Line where the exception occurred.
     * @param int        $context  Number of lines around the line where the exception occurred.
     * @return array
     */
    protected function getContext($template, $line, $context = 3)
    {
        $lines    = explode("\n", $template);
        $position = max(0, $line - $context);
        $max      = min(count($lines), $line - 1 + $context);

        $result = array();

        while ($position < $max) {
            $result[$position + 1] = $lines[$position];
            $position++;
        }

        return $result;
    }
}
