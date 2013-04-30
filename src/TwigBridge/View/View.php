<?php

namespace TwigBridge;

class View extends \Illuminate\View\View
{
    protected function getContents()
    {
        // We need to pass in the original view requested not just the path
        // which Illuminate\View\View does in order for Twig loader to get access
        // to view composers. This seems really like a poo hack?
        return $this->engine->get($this->path, $this->gatherData(), $this->view);
    }
}
