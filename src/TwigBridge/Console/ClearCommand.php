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

class ClearCommand extends Command {

    protected $name = 'twig:clear';
    protected $description = 'Clear the Twig Cache';


    protected $files;

    public function __construct(Twig_Environment $twig, Filesystem $files){
        $this->files = $files;
        $this->twig = $twig;

        parent::__construct();
    }

    public function fire(){

        $cacheDir = $this->twig->getCache();

        $this->files->deleteDirectory($cacheDir);

        if($this->files->exists($cacheDir)){
            $this->error('Could not clear Twig Cache..');
        }else{
            $this->info('Twig Cache cleared!');
        }

    }
}