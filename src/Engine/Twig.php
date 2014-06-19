<?php

/**
 * This file is part of the TwigBridge package.
 *
 * @copyright Robert Crowe <hello@vivalacrowe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TwigBridge\Engine;

use Illuminate\View\Engines\EngineInterface;
use Twig_Environment;
use Twig_Error_Loader;
use InvalidArgumentException;
use TwigBridge\Twig\Template;
use Illuminate\View\Engines\CompilerEngine;
use TwigBridge\Compiler\Twig as TwigCompiler;

/**
 * Twig engine for Laravel.
 */
class Twig extends CompilerEngine
{
    /**
     * @var TwigCompiler
     */
    protected $compiler;

    /**
     * @var array Global data that is always passed to the template.
     */
    protected $globalData = [];

    /**
     * Create a new instance of the Twig engine.
     *
     * @param TwigCompiler      $compiler
     * @param array             $globalData
     */
    public function __construct(TwigCompiler $compiler, array $globalData = [])
    {
        $this->compiler   = $compiler;
        $this->globalData = $globalData;
    }

    /**
     * Get the global data.
     *
     * @return array
     */
    public function getGlobalData()
    {
        return $this->globalData;
    }

    /**
     * Set global data sent to the view.
     *
     * @param array $globalData Global data.
     *
     * @return void
     */
    public function setGlobalData(array $globalData)
    {
        $this->globalData = $globalData;
    }

    /**
     * Loads the given template.
     *
     * @param string $name A template name
     *
     * @throws \InvalidArgumentException Thrown if the template does not exist.
     *
     * @return \Twig_TemplateInterface
     */
    public function load($name)
    {
        try {
            $template = $this->compiler->getTwig()->loadTemplate($name);
        } catch (Twig_Error_Loader $e) {
            throw new InvalidArgumentException("Error in $name: ". $e->getMessage(), $e->getCode(), $e);
        }

        if ($template instanceof Template) {
            // Events are already fired by the View Environment
            $template->setFiredEvents(true);
        }

        return $template;
    }

    /**
     * Get the evaluated contents of the view.
     *
     * @param string $path Full file path to Twig template.
     * @param array  $data
     *
     * @return string
     */
    public function get($path, array $data = [])
    {
        $data = array_merge($this->getGlobalData(), $data);

        // Render template
        return $this->load($path)->render($data);
    }
}
