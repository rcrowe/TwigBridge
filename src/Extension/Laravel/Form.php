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
use Illuminate\Html\FormBuilder;
use Illuminate\Support\Str;

/**
 * Access Laravels form builder in your Twig templates.
 */
class Form extends Twig_Extension
{
    /**
     * @var \Illuminate\Html\FormBuilder
     */
    protected $form;

    /**
     * Create a new form extension
     *
     * @param \Illuminate\Html\FormBuilder
     */
    public function __construct(FormBuilder $form)
    {
        $this->form = $form;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'TwigBridge_Extension_Laravel_Form';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction(
                'form_*',
                function ($name) {
                    $arguments = array_slice(func_get_args(), 1);
                    $name      = Str::camel($name);

                    return call_user_func_array([$this->form, $name], $arguments);
                },
                [
                    'is_safe' => ['html'],
                ]
            ),
        ];
    }
}
