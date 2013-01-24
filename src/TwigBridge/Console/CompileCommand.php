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
     * @param string $path      All twig files under this path are compiled.
     * @param string $namespace If path is part of a package, then the name-space hint.
     * @return void
     */
    protected function processPath($path, $namespace = null)
    {
        $path = realpath($path);

        $bridge = new TwigBridge($this->laravel);
        $finder = new Finder();
        $finder->files()->in($path)->name('*.'.$bridge->getExtension());

        foreach ($finder as $file) {

            $file = $file->getRealPath();

            // Handle files found in sub-folders
            $file = str_replace($path, '', $file);
            $file = ($file{0} === '/') ? substr($file, 1) : $file;
            $dir  = pathinfo($file, PATHINFO_DIRNAME);
            $dir  = ($dir !== '.') ? $dir.'/' : '';
            $file = $dir.pathinfo($file, PATHINFO_FILENAME);

            // Let Twig compile the view
            $this->laravel['view']->make($file)->render();
            $this->count++;
        }
    }
}
