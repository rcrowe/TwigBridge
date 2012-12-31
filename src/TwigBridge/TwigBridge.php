<?php

namespace TwigBridge;

use Illuminate\Foundation\Application;
use Twig_Environment;

class TwigBridge
{
    protected $app;
    protected $paths = array();
    protected $options = array();
    protected $extension;

    public function __construct(Application $app)
    {
        $this->app       = $app;
        $this->paths     = $app['config']->get('view.paths', array());
        $this->extension = $app['config']->get('twigbridge::extension');

        $this->setOptions($app['config']->get('twigbridge::twig', array()));
    }

    public function getPaths()
    {
        return $this->paths;
    }

    public function setPaths(array $paths)
    {
        $this->paths = $paths;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setOptions(array $options)
    {
        // Check whether we have the cache path set
        if (!isset($options['cache']) OR $options['cache'] === null) {

            // No cache path set for Twig, lets set to the Laravel views storage folder
            $options['cache'] = $this->app['path'].'/storage/views/twig';
        }

        $this->options = $options;
    }

    public function getExtension()
    {
        return $this->extension;
    }

    public function setExtension($extension)
    {
        $this->extension = $extension;
    }

    public function getTwig()
    {
        $loader = new Twig\Loader\Filesystem($this->paths, $this->extension);
        $twig   = new Twig_Environment($loader, $this->options);

        return $twig;
    }
}