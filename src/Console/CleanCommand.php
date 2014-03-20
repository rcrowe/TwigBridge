<?php

/**
 * Brings Twig to Laravel 4.
 *
 * @author Rob Crowe <hello@vivalacrowe.com>
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 * @license MIT
 */

namespace TwigBridge\Console;

use Illuminate\Console\Command;
use Twig_Environment;
use Illuminate\Filesystem\Filesystem;

/**
 * Artisan command to clear the Twig cache.
 */
class CleanCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'twig:clean';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Clean the Twig Cache';

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new console command instance.
     *
     * @param \Twig_Environment $twig
     * @param \Illuminate\Filesystem\Filesystem
     */
    public function __construct(Twig_Environment $twig, Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
        $this->twig  = $twig;
    }

    /**
     * {@inheritdoc}
     */
    public function fire()
    {
        $cacheDir = $this->twig->getCache();

        $this->files->deleteDirectory($cacheDir);

        if ($this->files->exists($cacheDir)) {
            $this->error('Twig cache failed to be cleaned');
        } else {
            $this->info('Twig cache cleaned');
        }
    }
}
