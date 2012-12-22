<?php

namespace TwigBridge;

use Illuminate\Foundation\Application;

class TwigBridge
{
    protected $app;
    protected $view_paths = array();
    protected $twig_options = array();

    public function __construct(Application $app)
    {
        $this->app        = $app;
        $this->view_paths = $app['config']->get('view.paths', array());

        $this->setOptions($app['config']->get('twigbridge::twig', array()));
    }

    public function getPaths()
    {
        return $this->view_paths;
    }

    public function setPaths(array $paths)
    {
        $this->view_paths = $paths;
    }

    public function getOptions()
    {
        return $this->twig_options;
    }

    public function setOptions(array $options)
    {
        // Check whether we have the cache path set
        if (!isset($options['cache']) OR $options['cache'] === null) {

            // No cache path set for Twig, lets set to the Laravel views storage folder
            $options['cache'] = $this->app['path'].'/storage/views/twig';
        }

        $this->twig_options = $options;
    }

    public function getTwig()
    {

    }
}