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
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $bridge = new TwigBridge($this->laravel);
        $count  = 0;

        // Process one folder at a time
        // Gets around the issue where files were clashing and not being compiled
        // when the file name existed in more than one path.
        foreach ($bridge->getTwig()->getLoader()->getPaths() as $path) {

            $twig = $bridge->getTwig(new Twig_Loader_Filesystem($path));

            $finder = new Finder();
            $finder->files()->in($path)->name('*.'.$bridge->getExtension());

            foreach ($finder as $file) {
                $file     = $file->getRealPath();
                $path     = pathinfo($file, PATHINFO_DIRNAME);
                $basename = pathinfo($file, PATHINFO_BASENAME);

                $twig->loadTemplate($basename);

                $count++;
            }
        }

        $this->info("{$count} Twig templates precompiled");
    }
}
