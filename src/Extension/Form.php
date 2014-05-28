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
use  Illuminate\Html\FormBuilder;
use  Illuminate\Support\Str;

class Form extends Twig_Extension
{
    protected $form;

    public function __construct(FormBuilder $form){
        $this->form = $form;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(){
        return 'TwigBridge_Extension_Form';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions(){
        $form = $this->form;
        return array(
            new Twig_SimpleFunction('form_*', function($name) use($form){
                $arguments = array_slice(func_get_args(), 1);
                $name = Str::camel($name);
                return call_user_func_array(array($form, $name), $arguments);
            }, array('is_safe' => array('html')))
        );
    }
}
