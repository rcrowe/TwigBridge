<?php

/**
 * Brings Twig to Laravel 4.
 *
 * @author Rob Crowe <hello@vivalacrowe.com>
 * @license MIT
 */

namespace TwigBridge\Engines;

use Illuminate\View\Engines\EngineInterface;
use Twig_Environment;

/**
 * Laravel view engine for Twig.
 */
class TwigEngine implements EngineInterface
{
    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * @var array Global data that is always passed to the template.
     */
    protected $global_data = array();

    /**
     * Create a new instance of the Twig engine.
     *
     * @param Twig_Environment $twig
     * @param array            $global_data
     */
    public function __construct(Twig_Environment $twig, array $global_data = array())
    {
        $this->twig        = $twig;
        $this->global_data = $global_data;
    }

    /**
     * Returns the instance of Twig used to render the template.
     *
     * @return Twig_Environment
     */
    public function getTwig()
    {
        return $this->twig;
    }

    /**
     * Set global data sent to the view.
     *
     * @param array $data Global data.
     * @return void
     */
    public function setGlobalData(array $data)
    {
        $this->global_data = $data;
    }

    /**
     * Get the global data.
     *
     * @return array
     */
    public function getGlobalData()
    {
        return $this->global_data;
    }

    /**
     * Get the data passed to the view. Merges with global.
     *
     * @param array $data View data.
     * @return array
     */
    public function getData(array $data)
    {
        return array_merge($this->getGlobalData(), $data);
    }

    /**
     * Load a Twig template (Does not render).
     *
     * @param  string  $path Full file path to Twig template.
     * @param  string  $view Original view passed View::make.
     * @return object  \Twig_TemplateInterface
     */
    public function load($path, $view)
    {
        // We need to move the directory requested as the first search path
        // this stops conflicts. For example, with packages
        $view_tmp = explode('/', str_replace('.', '/', $view));
        $path_tmp = explode('/', pathinfo($path, PATHINFO_DIRNAME));
        $path_tmp_slice = array_slice($path_tmp, 0, -(count($view_tmp)-1));
        $path_tmp_slice = implode('/', $path_tmp_slice);

        $paths[] = (strlen($path_tmp_slice) > 0) ? $path_tmp_slice : implode('/', $path_tmp);
        $paths   = array_merge($paths, $this->twig->getLoader()->getPaths());

        // Set new ordered paths
        $this->twig->getLoader()->setPaths($paths);

        return $this->twig->loadTemplate($view);
    }

    /**
     * Get the evaluated contents of the view.
     *
     * @param  string  $path Full file path to Twig template
     * @param  array   $data
     * @param  string  $view Original view passed View::make.
     * @return string
     */
    public function get($path, array $data = array(), $view = null)
    {
        // Render template
        return $this->load($path, $view)->render($this->getData($data));
    }
}
