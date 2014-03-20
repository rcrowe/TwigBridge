<?php

namespace TwigBridge\Extension\Loader;

use TwigBridge\Extension;

abstract class Loader extends Extension
{
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
