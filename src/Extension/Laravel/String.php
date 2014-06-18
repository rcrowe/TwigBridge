<?php

/**
 * This file is part of the TwigBridge package.
 *
 * @copyright Robert Crowe <hello@vivalacrowe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TwigBridge\Extension\Laravel;

use Twig_Extension;
use Twig_SimpleFunction;
use Twig_SimpleFilter;
use Illuminate\Support\Str;

/**
 * Access Laravels string class in your Twig templates.
 */
class String extends Twig_Extension
{
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'TwigBridge_Extension_Laravel_String';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction(
                'str_*',
                function ($name) {
                    $arguments = array_slice(func_get_args(), 1);
                    $name      = Str::camel($name);

                    return call_user_func_array(['Illuminate\Support\Str', $name], $arguments);
                },
                [
                    'is_safe' => ['html'],
                ]
            ),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getFilters()
    {
        return [
            new Twig_SimpleFilter('camel_case', ['Illuminate\Support\Str', 'camel'], ['is_safe' => ['html']]),
            new Twig_SimpleFilter('snake_case', ['Illuminate\Support\Str', 'snake'], ['is_safe' => ['html']]),
            new Twig_SimpleFilter('studly_case', ['Illuminate\Support\Str', 'studly'], ['is_safe' => ['html']]),
            new Twig_SimpleFilter(
                'str_*',
                function ($name) {
                    $arguments = array_slice(func_get_args(), 1);
                    $name      = Str::camel($name);

                    return call_user_func_array(['Illuminate\Support\Str', $name], $arguments);
                },
                [
                    'is_safe' => ['html'],
                ]
            ),
        ];
    }
}
