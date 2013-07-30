<?php

/**
 * Brings Twig to Laravel.
 *
 * @author Rob Crowe <hello@vivalacrowe.com>
 * @license MIT
 */

namespace TwigBridge\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

/**
 * Remove compiled Twig templates.
 */
class CleanCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'twig:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Empty the Twig cache';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        // Get the path to where Twig cache lives
        $cache_path = $this->laravel['config']->get('twigbridge::twig.cache');

        if ($cache_path === null OR !file_exists($cache_path)) {
            $cache_path = $this->laravel['path.storage'].'/views/twig';
        }

        if (file_exists($cache_path)) {

            $file = new Filesystem;
            $file->deleteDirectory($cache_path);

            if (!file_exists($cache_path)) {
                $this->info('Twig cache cleaned');
            } else {
                $this->error('Twig cache failed to be cleaned');
            }

            return;
        }

        $this->info('Twig cache cleaned');
    }
}
