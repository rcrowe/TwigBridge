<?php

namespace TwigBridge\View;

use Illuminate\View\Factory as LaravelFactory;
use TwigBridge\Engine\Twig;

class Factory extends LaravelFactory
{
    protected function viewInstance($view, $path, $data)
    {
        $engine = $this->getEngineFromPath($path);
        if ($engine instanceof Twig) {
            return new View($this, $engine, $view, $path, $data);
        }

        return parent::viewInstance($view, $path, $data);
    }
}
