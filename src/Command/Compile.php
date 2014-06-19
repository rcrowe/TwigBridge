<?php

/**
 * This file is part of the TwigBridge package.
 *
 * @copyright Robert Crowe <hello@vivalacrowe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TwigBridge\Command;

use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;

class Compile extends Command
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
     * @var \TwigBridge\Bridge
     */
    protected $twig;

    /**
     * @var \Symfony\Component\Finder\Finder
     */
    protected $finder;

    /**
     * Get a finder instance of Twig files in the specified directories.
     *
     * @param array $paths Paths to search for files in.
     *
     * @return \Symfony\Component\Finder\Finder
     */
    public function getFinder(array $paths)
    {
        $finder = (empty($this->finder)) ? Finder::create() : $this->finder;

        return $finder->files()->in($paths)->name('*.'.$this->laravel['twig.extension']);
    }

    /**
     * Set the finder used to search for Twig files.
     *
     * @param \Symfony\Component\Finder\Finder $finder
     *
     * @return void
     */
    public function setFinder(Finder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * {@inheritdoc}
     */
    public function fire()
    {
        $this->line('Compiling Twig templates. Environment: <comment>'.$this->laravel->make('env').'</comment>');

        $this->twig = $this->laravel['twig.bridge'];
        $finder     = $this->laravel['view']->getFinder();
        $paths      = [];

        // Process views
        foreach ($finder->getPaths() as $path) {
            $paths[] = $path;
        }

        // Process packages
        foreach ($finder->getHints() as $namespace => $package_paths) {
            foreach ($package_paths as $path) {
                $paths[] = $path;
            }
        }

        $count = $this->processPaths($paths);
        $msg   = ($count > 1 || $count === 0) ? 'templates' : 'template';
        $msg   = sprintf('%s Twig %s compiled', $count, $msg);

        $this->info($msg);
    }

    /**
     * Iterate over all Twig files in the paths and compile.
     *
     * @param array $paths Paths to search for Twig files in.
     *
     * @return int Number of files compiled.
     */
    protected function processPaths(array $paths)
    {
        $count = 0;
        foreach ($this->getFinder($paths) as $file) {
            $this->twig->render($file->getRealPath());
            $count++;
        }

        return $count;
    }
}
