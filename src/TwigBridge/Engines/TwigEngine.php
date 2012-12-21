<?php

namespace TwigBridge\Engines;

use Illuminate\View\Engines\EngineInterface;
use Twig_Environment;

class TwigEngine implements EngineInterface
{
    /**
     * Create a new instance of the Twig engine.
     *
     * @param Twig_Environment $twig
     * @return void
     */
    public function __construct(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function getTwig()
    {
        return $this->twig;
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

        return $this->twig->loadTemplate($path)->render($data);
    }
}