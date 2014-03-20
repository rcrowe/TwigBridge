<?php

/**
 * Brings Twig to Laravel.
 *
 * @author Rob Crowe <hello@vivalacrowe.com>
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 * @license MIT
 */
namespace TwigBridge\Extension\Facade;

use Twig_Markup;

class Caller
{
    /**
     * @var string The name of the facade that has to be called
     */
    protected $facade;

    /**
     * @var array Customisation options for the called facade / method.
     */
    protected $options;

    /**
     * Create a new caller for a facade.
     *
     * @param string $facade
     * @param array  $options
     */
    public function __construct($facade, array $options = array())
    {
        $this->facade  = $facade;
        $this->options = array_merge(
            array(
                'is_safe' => null,
                'charset' => null,
            ),
            $options
        );
    }

    /**
     * Make a 'magic' call to a facade (or static class method)
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($method, array $arguments)
    {
        $is_safe = ($this->options['is_safe'] === true);

        // Allow is_safe option to specify individual methods of the facade that are safe
        if (is_array($this->options['is_safe']) && in_array($method, $this->options)) {
            $is_safe = true;
        }

        $result  = forward_static_call_array(array($this->facade, $method), $arguments);
        $is_safe = ($is_safe && (is_string($result) || method_exists($result, '__toString')));

        return ($is_safe) ? new Twig_Markup($result, $this->options['charset']) : $result;
    }
}
