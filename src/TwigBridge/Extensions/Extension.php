<?php

namespace TwigBridge\Extensions;

use Illuminate\Foundation\Application;
use Twig_Extension;
use Twig_Environment;

abstract class Extension extends Twig_Extension
{
    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * @var Illuminate\Foundation\Application
     */
    protected $app;

    public function __construct(Application $app, Twig_Environment $twig)
    {
        $this->app  = $app;
        $this->twig = $twig;
    }
}