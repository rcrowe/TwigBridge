<?php

namespace TwigBridge\Extensions;

use Illuminate\Foundation\Application;
use Twig_Extension;

abstract class Extension extends Twig_Extension
{
    /**
     * @var Illuminate\Foundation\Application
     */
    protected $app;

    public function setApp(Application $app)
    {
        $this->app = $app;
    }
}