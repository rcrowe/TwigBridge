<?php

namespace TwigBridge\Extension\Loader;

use Twig_SimpleFilter;

class Filters extends Loader
{
    /**
     * Returns the name of the extension.
     *
     * @return string Extension name.
     */
    public function getName()
    {
        return 'TwigBridge_Extension_Loader_Filters';
    }

    /**
     * Get filters this extensions provides.
     *
     * @return array
     */
    public function getFilters()
    {
        $load    = $this->app['config']->get('twigbridge::extensions.filters', array());
        $filters = array();

        foreach ($load as $method => $callable) {
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
}
