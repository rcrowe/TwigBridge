<?php

namespace TwigBridge\Extensions;

use Illuminate\Foundation\Application;
use Twig_Environment;
use Twig_Function_Function;

class AliasLoader extends Extension
{
    /**
     * @var array Lookup cache
     */
    protected $lookup = array();

    /**
     * @var array Aliases loaded by Illuminate.
     */
    protected $aliases;

    /**
     * @var array Shortcuts to alias functions.
     */
    protected $shortcuts;

    public function getName()
    {
        return 'AliasLoader';
    }

    public function __construct(Application $app, Twig_Environment $twig)
    {
        parent::__construct($app, $twig);

        $aliases   = $app['config']->get('app.aliases', array());
        $shortcuts = $app['config']->get('twigbridge::alias_shortcuts', array());

        $this->setAliases($aliases);
        $this->setShortcuts($shortcuts);

        $loader = $this;

        $twig->registerUndefinedFunctionCallback(function($name) use($loader) {
            // Allow any method on aliased classes
            return $loader->getFunction($name);
        });
    }

    public function setAliases(array $aliases)
    {
        $this->aliases = array_change_key_case($aliases, CASE_LOWER);
    }

    public function getAliases()
    {
        return $this->aliases;
    }

    public function setShortcuts(array $shortcuts)
    {
        $lowered = array();

        foreach ($shortcuts as $from => $to) {
            $lowered[strtolower($from)] = strtolower($to);
        }

        $this->shortcuts = $lowered;
    }

    public function getShortcuts()
    {
        return $this->shortcuts;
    }

    public function getShortcut($name)
    {
        $name = strtolower($name);
        return (array_key_exists($name, $this->shortcuts)) ? $this->shortcuts[$name] : $name;
    }

    public function getAliasParts($name)
    {
        $name = strtolower($name);

        if (strpos($name, '_') !== false) {
            $parts = explode('_', $name);
            $parts = array_filter($parts); // Remove empty elements
            return (count($parts) < 2) ? false : $parts;
        }

        return false;
    }

    public function getLookup($name)
    {
        $name = strtolower($name);
        return (array_key_exists($name, $this->lookup)) ? $this->lookup[$name] : false;
    }

    public function setLookup($name, Twig_Function_Function $function)
    {
        $this->lookup[strtolower($name)] = $function;
    }

    public function getFunction($name)
    {
        $name = $this->getShortcut(strtolower($name));

        // Check if we have looked this alias up before
        if ($function = $this->getLookup($name)) {
            return $function;
        }

        // Get the class / method we are trying to call
        $parts = $this->getAliasParts($name);

        if ($parts === false) {
            return false;
        }

        list($class, $method) = $parts;

        // Does that alias exist
        if (array_key_exists($class, $this->aliases)) {

            $class = $this->aliases[$class];

            if (is_callable($class.'::'.$method)) {

                $function = new Twig_Function_Function($class.'::'.$method);
                $this->setLookup($name, $function);
                return $function;
            }
        }

        return false;
    }
}
