<?php

/**
 * This file is part of the TwigBridge package.
 *
 * @copyright Robert Crowe <hello@vivalacrowe.com>
 * @copyright Barry vd. Heuvel <barryvdh@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TwigBridge\Extension;

use Twig_Extension;
use Twig_SimpleFunction;
use Twig_SimpleFilter;
use Illuminate\Support\Str;

class String extends Twig_Extension
{

    /**
     * {@inheritDoc}
     */
    public function getName(){
        return 'TwigBridge_Extension_String';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions(){
        return array(
            new Twig_SimpleFunction('str_*', function($name){
                $arguments = array_slice(func_get_args(), 1);
                $name = Str::camel($name);
                return call_user_func_array(array('Illuminate\Support\Str', $name), $arguments);
            }, array('is_safe' => array('html'))),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getFilters(){
        return array(
            new \Twig_SimpleFilter('camel_case', array('Illuminate\Support\Str', 'camel'), array('is_safe' => array('html'))),
            new \Twig_SimpleFilter('snake_case', array('Illuminate\Support\Str', 'snake'), array('is_safe' => array('html'))),
            new \Twig_SimpleFilter('studly_case', array('Illuminate\Support\Str', 'studly'), array('is_safe' => array('html'))),
            new \Twig_SimpleFilter('str_*', function($name){
                $arguments = array_slice(func_get_args(), 1);
                $name = Str::camel($name);
                return call_user_func_array(array('Illuminate\Support\Str', $name), $arguments);
            }, array('is_safe' => array('html'))),
        );
    }

}
