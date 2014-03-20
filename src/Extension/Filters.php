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

        foreach ($this->filters as $key => $value) {
            if (is_int($key)) {
                $filter   = $value;
                $callback = $value;
                $options  = array();
            } elseif (is_string($value)) {
                $filter   = $key;
                $callback = $value;
                $options  = array();
            } elseif (is_array($value)) {
                $filter  = $key;
                $options = array_merge(
                    array(
                        'callback' => null,
                        'options'  => array(),
                    ),
                    $value,
                );

                if (empty($options['callback'])) {
                    throw new InvalidArgumentException('Filter `'.$filter.'` has no callback option!');
                }

                if (!is_callable($options['callback'])) {
                    throw new InvalidArgumentException('Filter `'.$filter.'` callback is not callable!');
                }

                $callback = $options['callback'];
                $options  = $options['options'];
            } else {
                throw new InvalidArgumentException('Incorrect filter');
            }

            $function = new Twig_SimpleFilter(
                $filter,
                function () use ($callback) {
                    return call_user_func_array($callback, func_get_args());
                },
                $options
            );

            $filters[] = $function;
        }

        return $filters;
    }
}
