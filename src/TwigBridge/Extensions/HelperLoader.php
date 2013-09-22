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
use Twig_Function_Function;
use InvalidArgumentException;

/**
 * Handles easy usage of filters/functions in Twig
 *
 */
class HelperLoader extends Extension
{

    protected $filters;
    protected $functions;
    /**
     * Create a new extension instance. Registers Twig undefined function callback.
     *
     * @param \Illuminate\Foundation\Application|\Illuminate\Foundation\Application $app
     * @param Twig_Environment $twig
     */
    public function __construct(Application $app, Twig_Environment $twig)
    {
        parent::__construct($app, $twig);

        $this->functions = $app['config']->get('twigbridge::config.functions', array());
        $this->filters = $app['config']->get('twigbridge::config.filters', array());

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

    public function getFunctions(){

        $functions = array();

        foreach ($this->functions as $method => $twigFunction) {
            if (is_string($twigFunction)) {
                $methodName = $twigFunction;
            } elseif (is_callable($twigFunction)) {
                $methodName = $method;
            } else {
                throw new InvalidArgumentException('Incorrect function type');
            }

            $function = new \Twig_SimpleFunction($methodName, function () use ($twigFunction) {
                return call_user_func_array($twigFunction, func_get_args());
            });

            $functions[] = $function;
        }

        return $functions;
    }

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

            $function = new \Twig_SimpleFilter($methodName, function () use ($twigFilter) {
                return call_user_func_array($twigFilter, func_get_args());
            });

            $filters[] = $function;
        }

        return $filters;
    }


}
