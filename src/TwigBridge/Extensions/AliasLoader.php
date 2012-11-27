<?php

namespace TwigBridge\Extensions;

use Twig_Function_Function;

/**
 * If enabled, when a function used in a Twig template can not be found
 * we fall back to calling those classes defined in the alias array.
 *
 * These means we get nicer integration with Laravel functions.
 */
class AliasLoader
{
    /**
     * @var array Aliases loaded by Illuminate.
     */
    protected $aliases;

    /**
     * @var array Shortcuts to alias functions.
     */
    protected $shortcuts;

    public function __construct(array $aliases = array(), array $shortcuts = array())
    {
        $this->aliases   = array_change_key_case($aliases, CASE_LOWER);
        $this->shortcuts = array_change_key_case($shortcuts, CASE_LOWER);
    }

    /**
     * Get the function responsible for the Twig function.
     *
     * @param string $name Name of the Twig function.
     * @return Twig_Function_Function|false False if the function can not be found.
     */
    public function getFunction($name)
    {
        // Check for user defined alias of Twig functions
        // Not currently implemented
        if (array_key_exists($name, $this->shortcuts)) {
            $name = $this->shortcuts[$name];
        }

        // Using alias loader, twig function must follow the pattern: class_method
        // Check for this pattern
        if (strpos($name, '_') !== false) {

            list($class, $method) = explode('_', $name);
            $class = strtolower($class);

            // Does that alias exist
            if (array_key_exists($class, $this->aliases)) {

                $class = $this->aliases[$class];

                if (is_callable($class.'::'.$method)) {
                    return new Twig_Function_Function($class.'::'.$method);
                }
            }
        }

        return false;
    }
}