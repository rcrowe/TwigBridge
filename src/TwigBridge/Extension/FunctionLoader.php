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
use Twig_SimpleFunction;
use InvalidArgumentException;

/**
 * Lets Twig access configurable functions and filters.
 */
class FunctionLoader extends Extension
{

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

        $this->functions = $app['config']->get('twigbridge::extensions.functions', array());
    }

    /**
     * Returns the name of the extension.
     *
     * @return string Extension name.
     */
    public function getName()
    {
        return 'FunctionLoader';
    }

    /**
     * Get functions this extensions provides.
     *
     * @throws \InvalidArgumentException
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

}
