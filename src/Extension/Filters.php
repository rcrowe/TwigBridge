<?php

/**
 * Brings Twig to Laravel.
 *
 * @author Rob Crowe <hello@vivalacrowe.com>
 * @license MIT
 */

namespace TwigBridge\Extension;

use Illuminate\Foundation\Application;
use TwigBridge\Extension;
use Twig_Environment;
use Twig_SimpleFilter;
use InvalidArgumentException;

/**
 * Lets Twig access configurable functions and filters.
 */
class Filters extends Extension
{
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

        $this->filters = $app['config']->get('twigbridge::extensions.filters', array());
    }

    /**
     * Returns the name of the extension.
     *
     * @return string Extension name.
     */
    public function getName()
    {
        return 'TwigBridge_Filters';
    }

    /**
     * Get filters this extensions provides.
     *
     * @throws \InvalidArgumentException
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
