<?php

namespace TwigBridge\Console;

use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;
use TwigBridge\TwigBridge;
use Twig_Environment;
use Twig_Loader_Filesystem;
use Twig_Error_Loader;

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
    protected $count;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $view_paths      = $this->laravel['view']->getFinder()->getPaths();
        $namespace_paths = $this->laravel['view']->getFinder()->getHints();

        foreach ($view_paths as $path) {
            $this->processPath($path);
        }

        foreach ($namespace_paths as $namespace => $paths) {
            foreach ($paths as $path) {
                $this->processPath($path, $namespace);
            }
        }

        $this->info($this->count.' Twig templates compiled');
    }

    protected function processPath($path, $namespace = null)
    {
        $path = realpath($path);

        $bridge = new TwigBridge($this->laravel);
        $finder = new Finder();
        $finder->files()->in($path)->name('*.'.$bridge->getExtension());

        foreach ($finder as $file) {

            $file = $file->getRealPath();
            $file = pathinfo($file, PATHINFO_FILENAME);
            $file = ($namespace === null) ? $file : $namespace.'::'.$file;

            $this->laravel['view']->make($file)->render();
            $this->count++;
        }
    }
}
