<?php

/**
 * This file is part of the TwigBridge package.
 *
 * @copyright Robert Crowe <hello@vivalacrowe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TwigBridge;

use Twig_Extension;
use Illuminate\Foundation\Application;
use Twig_Environment;

/**
 * Base extension that most extensions should extend.
 *
 * Provides the extension with better integration with your application
 * as an instance of your app is passed in.
 */
abstract class Extension extends Twig_Extension
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * Create a new extension instance. Registers Twig undefined function callback.
     *
     * @param \Illuminate\Foundation\Application $app
     * @param \Twig_Environment                  $twig
     */
    public function __construct(Application $app, Twig_Environment $twig)
    {
        $this->app  = $app;
        $this->twig = $twig;
    }

    /**
     * Get the application instance.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * Get the Twig instance.
     *
     * @return \Twig_Environment
     */
    public function getTwig()
    {
        return $this->twig;
    }
}
