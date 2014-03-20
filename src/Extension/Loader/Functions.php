<?php

namespace TwigBridge\Extension\Loader;

use Twig_SimpleFunction;

class Functions extends Loader
{
    /**
     * Returns the name of the extension.
     *
     * @return string Extension name.
     */
    public function getName()
    {
        return 'TwigBridge_Extension_Loader_Functions';
    }

    /**
     * Get functions this extensions provides.
     *
     * @return array
     */
    public function getFunctions()
    {
        $load      = $this->app['config']->get('twigbridge::extensions.functions', array());
        $functions = array();

        foreach ($load as $method => $callable) {
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
}
