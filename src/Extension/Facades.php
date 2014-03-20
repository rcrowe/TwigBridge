<?php

/**
 * Brings Twig to Laravel.
 *
 * @author Rob Crowe <hello@vivalacrowe.com>
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 * @license MIT
 */
namespace TwigBridge\Extension;

use TwigBridge\Extension;
use Illuminate\Foundation\Application;
use Twig_Environment;

/**
 * Let's Twig access Facades using global methods (ie. Config.get('app.debug'))
 */
class Facades extends Extension
{
    /**
     * @var array List of facades to add as globals.
     */
    protected $facades;

    /**
     * Registers a new Extension and loads the facades from the config.
     *
     * @param Application $app
     * @param Twig_Environment $twig
     */
    public function __construct(Application $app, Twig_Environment $twig)
    {
        parent::__construct($app, $twig);

        $this->facades = $app['config']->get('twigbridge::extensions.facades', array());
    }

    /**
     * Returns the name of the extension.
     *
     * @return string Extension name.
     */
    public function getName()
    {
        return 'TwigBridge_Facades';
    }

    /**
     * Return all globals created by this FacadeLoader
     *
     * @return array
     */
    public function getGlobals()
    {
        $facades = array();

        foreach ($this->facades as $facade) {
            $caller           = new Facade\Caller($facade);
            $facades[$facade] = $caller;
        }

        return $facades;
    }
}
