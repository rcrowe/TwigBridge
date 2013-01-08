<?php

namespace TwigBridge;

use Twig_Extension;
use Illuminate\Foundation\Application;
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

    /**
     * Create a new extension instance. Registers Twig undefined function callback.
     *
     * @param Illuminate\Foundation\Application $app
     * @param Twig_Environment                  $twig
     */
    public function __construct(Application $app, Twig_Environment $twig)
    {
        $this->app  = $app;
        $this->twig = $twig;
    }

    /**
     * Get the application instance.
     *
     * @return Illuminate\Foundation\Application
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * Get the Twig instance.
     *
     * @return Twig_Environment
     */
    public function getTwig()
    {
        return $this->twig;
    }
}