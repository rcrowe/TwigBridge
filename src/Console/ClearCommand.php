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

class ClearCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'twig:clear';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Clear the Twig Cache';

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
            $this->error('Could not clear Twig Cache..');
        } else {
            $this->info('Twig Cache cleared!');
        }
    }
}
