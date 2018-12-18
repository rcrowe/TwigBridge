<?php

namespace TwigBridge\Command;

use Illuminate\Console\Command;

/**
 * Artisan command to clear the Twig cache.
 */
class Clean extends Command
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
     * {@inheritdoc}
     */
    public function handle()
    {
        $twig     = $this->laravel['twig'];
        $files    = $this->laravel['files'];
        $cacheDir = $twig->getCache();

        $files->deleteDirectory($cacheDir);

        if ($files->exists($cacheDir)) {
            $this->error('Twig cache failed to be cleaned');
        } else {
            $this->info('Twig cache cleaned');
        }
    }
}
