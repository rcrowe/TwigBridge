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
use TwigBridge\Engines\TwigEngine;

/**
 * Pre-compile Twig templates so it doesn't have to be done at runtime.
 */
class CompileCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'twig:compile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '(Pre) Compile templates';

    /**
     * @var int Number of files processed
     */
    protected $count = 0;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->line('Compiling Twig templates. Environment: <comment>'.$this->laravel->make('env').'</comment>');

        // Process view paths
        foreach ($this->laravel['view']->getFinder()->getPaths() as $path) {
            $this->processPath($path);
        }

        // Process packages
        foreach ($this->laravel['view']->getFinder()->getHints() as $namespace => $paths) {
            foreach ($paths as $path) {
                $this->processPath($path, $namespace);
            }
        }

        $msg = ($this->count > 1 OR $this->count === 0) ? 'templates' : 'template';
        $msg = sprintf('%s Twig %s compiled', $this->count, $msg);

        $this->info($msg);
    }

    /**
     * Compiles all Twig files under path to cache directory.
     *
     * @param string $path All twig files under this path are compiled.
     * @return void
     */
    protected function processPath($path)
    {
        $path = realpath($path);

        $bridge = new TwigBridge($this->laravel);
        $engine = new TwigEngine($bridge->getTwig());
        $finder = new Finder();
        $finder->files()->in($path)->name('*.'.$bridge->getExtension());

        foreach ($finder as $file) {

            $full_path = $file->getRealPath();

            // Handle files found in sub-folders
            $view = str_replace($path, '', $full_path);
            $view = ($view{0} === '/') ? substr($view, 1) : $view;
            $dir  = pathinfo($view, PATHINFO_DIRNAME);
            $dir  = ($dir !== '.') ? $dir.'/' : '';
            $view = $dir.pathinfo($view, PATHINFO_FILENAME);

            // Let Twig compile the view
            $engine->load($full_path, $view);
            $this->count++;
        }
    }
}
