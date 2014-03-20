<?php

/**
 * Brings Twig to Laravel.
 *
 * @author Rob Crowe <hello@vivalacrowe.com>
 * @license MIT
 */

namespace TwigBridge\Extension;

use TwigBridge\Extension;
use Illuminate\Foundation\Application;
use Twig_Environment;
use Twig_SimpleFunction;
use Twig_SimpleFilter;
use InvalidArgumentException;

/**
 * Lets Twig access configurable functions and filters.
 */
class Loader extends Extension
{
    /**
     * @var array Twig functions.
     */
    protected $functions;

    /**
     * @var array Twig filters.
     */
    protected $filters;

    /**
     * Create a new extension instance.
     *
     * @param \Illuminate\Foundation\Application|\Illuminate\Foundation\Application $app
     * @param Twig_Environment                                                      $twig
     */
    public function __construct(Application $app, Twig_Environment $twig)
    {
        parent::__construct($app, $twig);

        $this->functions = $app['config']->get('twigbridge::extensions.functions', array());
        $this->filters   = $app['config']->get('twigbridge::extensions.filters', array());
        $this->facades   = $app['config']->get('twigbridge::extensions.facades', array());
    }

    /**
     * Returns the name of the extension.
     *
     * @return string Extension name.
     */
    public function getName()
    {
        return 'TwigBridge_Extension_Loader';
    }

    /**
     * Get functions this extensions provides.
     *
     * @return array
     */
    public function getFunctions()
    {
        $functions = array();

        foreach ($this->functions as $method => $callable) {
            list($method, $callable, $options) = $this->parseCallable($method, $callable);

            $function = new Twig_SimpleFunction(
                $method,
                function () use ($callable) {
                    return call_user_func_array($callable, func_get_args());
                },
                $options
            );

            $functions[] = $function;
        }

        return $functions;
    }

    /**
     * Get filters this extensions provides.
     *
     * @return array
     */
    public function getFilters()
    {
        $filters = array();

        foreach ($this->filters as $method => $callable) {
            list($method, $callable, $options) = $this->parseCallable($method, $callable);

            $filter = new Twig_SimpleFilter(
                $method,
                function () use ($callable) {
                    return call_user_func_array($callable, func_get_args());
                },
                $options
            );

            $filters[] = $filter;
        }

        return $filters;
    }

    /**
     * Get globals this extension provides.
     *
     * Currently only returns facades.
     *
     * @return array
     */
    public function getGlobals()
    {
        $globals = array();

        foreach ($this->facades as $facade => $options) {
            list($facade, $callable, $options) = $this->parseCallable($facade, $options);

            $globals[$facade] = new Facade\Caller($facade, $options);
        }

        return $globals;
    }

    /**
     * Parse callable & options.
     *
     * @param int|string   $method
     * @param string|array $callable
     *
     * @return array
     */
    protected function parseCallable($method, $callable)
    {
        $options = array();

        if (is_array($callable)) {
            $options = $callable;

            if (isset($options['callback'])) {
                $callable = $options['callback'];
                unset($options['callback']);
            } else {
                $callable = $method;
            }
        }

        // Support Laravel style class@method syntax
        if (is_string($callable)) {
            // Check for numeric index
            if (!is_string($method)) {
                $method = $callable;
            }

            if (strpos($callable, '@') !== false) {
                $callable = explode('@', $callable, 2);
            }
        }

        return array($method, $callable, $options);
    }
}
