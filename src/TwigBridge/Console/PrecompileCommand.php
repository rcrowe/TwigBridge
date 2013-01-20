<?php

namespace TwigBridge\Console;

use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;
use TwigBridge\TwigBridge;
use Twig_Error_Loader;

class PrecompileCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'twig:precompile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Precompile app templates';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $twig = new TwigBridge($this->laravel);
        $twig = $twig->getTwig();

        $paths = $twig->getLoader()->getPaths();

        $finder = new Finder();
        $finder->files()->in($this->laravel['path'])->name('*.twig');

        $count = 0;
        foreach ($finder as $file) {
            $fname = realpath($file->getRealPath());
            foreach ($paths as $path) {
                try {
                    $twig->loadTemplate(str_replace(realpath($path), '', $fname));
                    $count++;
                } catch (Twig_Error_Loader $e) {
                    // File not found at this path, continue.
                }
            }
        }

        $this->info("{$count} Twig templates precompiled");
    }
}
