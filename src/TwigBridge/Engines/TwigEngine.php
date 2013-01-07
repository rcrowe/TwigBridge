<?php

namespace TwigBridge\Engines;

use Illuminate\View\Engines\EngineInterface;
use Twig_Environment;

class TwigEngine implements EngineInterface
{
    /**
     * @var array Set global variables that are always set.
     */
    protected $global_data = array();

    /**
     * Create a new instance of the Twig engine.
     *
     * @param Twig_Environment $twig
     * @return void
     */
    public function __construct(Twig_Environment $twig, array $global_data = array())
    {
        $this->twig        = $twig;
        $this->global_data = $global_data;
    }

    public function getTwig()
    {
        return $this->twig;
    }

    public function setGlobalData(array $data)
    {
        $this->global_data = $data;
    }

    public function getGlobalData()
    {
        return $this->global_data;
    }

    public function getData(array $data)
    {
        return array_merge($this->getGlobalData(), $data);
    }

    /**
     * Get the evaluated contents of the view.
     *
     * @param  string  $path
     * @param  array   $data
     * @return string
     */
    public function get($path, array $data = array())
    {
        $paths = $this->twig->getLoader()->getPaths();

        // Look for file in the search paths
        // Finds the first occurrence. Search path order is FIFO.
        foreach ($paths as $search_path) {
            if (strpos($path, $search_path) !== false) {
                $path = substr($path, strlen($search_path));
                break;
            }
        }

        return $this->twig->loadTemplate($path)->render($this->getData($data));
    }
}