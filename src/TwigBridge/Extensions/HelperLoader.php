<?php

/**
 * Brings Twig to Laravel.
 *
 * @author Rob Crowe <hello@vivalacrowe.com>
 * @license MIT
 */

namespace TwigBridge\Extensions;

use Illuminate\Foundation\Application;
use TwigBridge\Extension;
use Twig_Environment;
use Twig_SimpleFunction;
use Twig_SimpleFilter;
use InvalidArgumentException;

/**
 * Lets Twig access configurable functions and filters.
 */
class HelperLoader extends Extension
{
    /**
     * Twig filters.
     *
     * @var array
     */
    protected $filters;

    /**
     * Twig functions.
     *
     * @var array
     */
    protected $functions;

    /**
     * Create a new extension instance.
     *
     * @param \Illuminate\Foundation\Application|\Illuminate\Foundation\Application $app
     * @param Twig_Environment                                                      $twig
     */
    public function __construct(Application $app, Twig_Environment $twig)
    {
        parent::__construct($app, $twig);

        $this->functions = $app['config']->get('twigbridge::functions', array());
        $this->filters   = $app['config']->get('twigbridge::filters', array());
    }

    /**
     * Returns the name of the extension.
     *
     * @return string Extension name.
     */
    public function getName()
    {
        return 'HelperLoader';
    }

    /**
     * Get functions this extensions provides.
     *
     * @return array
     */
    public function getFunctions()
    {
        $functions = array();

        foreach ($this->functions as $method => $twigFunction) {

            if (is_string($twigFunction)) {
                $methodName = $twigFunction;
            } elseif (is_callable($twigFunction)) {
                $methodName = $method;
            } else {
                throw new InvalidArgumentException('Incorrect function type');
            }

            $function = new Twig_SimpleFunction($methodName, function () use ($twigFunction) {
                return call_user_func_array($twigFunction, func_get_args());
            });

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

        foreach ($this->filters as $method => $twigFilter) {
            if (is_string($twigFilter)) {
                $methodName = $twigFilter;
            } elseif (is_callable($twigFilter)) {
                $methodName = $method;
            } else {
                throw new InvalidArgumentException('Incorrect function filter');
            }

            $function = new Twig_SimpleFilter($methodName, function () use ($twigFilter) {
                return call_user_func_array($twigFilter, func_get_args());
            });

            $filters[] = $function;
        }

        return $filters;
    }
}
