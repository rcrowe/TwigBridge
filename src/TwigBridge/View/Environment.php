<?php

namespace TwigBridge\View;

class Environment extends \Illuminate\View\Environment
{
    public function make($view, $data = array(), $mergeData = array())
    {
        $path = $this->finder->find($view);
        $data = array_merge($data, $mergeData);

        return new View($this, $this->getEngineFromPath($path), $view, $path, $data);
    }
}
