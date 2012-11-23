<?php

namespace TwigBridge\Engines;

use Illuminate\View\Engines\EngineInterface;
// use Twig_Environment;

class TwigEngine implements EngineInterface
{
    public function __construct($twig)
    {
        $this->twig = $twig;
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

        foreach ($paths as $search_path) {
            if (strpos($path, $search_path) !== false) {
                $path = substr($path, strlen($search_path));
                break;
            }
        }

        return $this->twig->loadTemplate($path)->render($data);
    }
}