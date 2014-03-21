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
use Twig_Template;
use Twig_Error_Loader;
use InvalidArgumentException;

/**
 * Twig engine for Laravel.
 */
class Twig implements EngineInterface
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var array Global data that is always passed to the template.
     */
    protected $globalData = array();

    /**
     * Create a new instance of the Twig engine.
     *
     * @param \Twig_Environment $twig
     * @param array             $globalData
     */
    public function __construct(Twig_Environment $twig, array $globalData = array())
    {
        $this->twig       = $twig;
        $this->globalData = $globalData;
    }

    /**
     * Returns the instance of Twig used to render the template.
     *
     * @return \Twig_Environment
     */
    public function getTwig()
    {
        return $this->twig;
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
     * @param string|\Twig_Template $name A template name or an instance of Twig_Template
     *
     * @throws \InvalidArgumentException if the template does not exist
     *
     * @return \Twig_TemplateInterface
     */
    public function load($name)
    {
        if ($name instanceof Twig_Template) {
            return $name;
        }

        try {
            return $this->twig->loadTemplate($name);
        } catch (Twig_Error_Loader $e) {
            throw new InvalidArgumentException("Error in $name: ". $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get the evaluated contents of the view.
     *
     * @param  string $path Full file path to Twig template.
     * @param  array  $data
     * @param  string $view Original view passed View::make.
     *
     * @return string
     */
    public function get($path, array $data = array(), $view = null)
    {
        $data = array_merge($this->getGlobalData(), $data);

        // Render template
        return $this->load($path)->render($data);
    }
}
