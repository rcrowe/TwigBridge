<?php

/**
 * Brings Twig to Laravel 4.
 *
 * @author Rob Crowe <hello@vivalacrowe.com>
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 * @license MIT
 */

namespace TwigBridge\Twig;

use Twig_Template;
use Illuminate\View\View;

/**
 * Default base class for compiled templates.
 */
abstract class Template extends Twig_Template
{

    /**
     * {@inheritdoc}
     */
    public function display( array $context, array $blocks = array())
    {

        if($this->shouldFireEvents()){
            $context = $this->fireEvents($context);
        }

        parent::display($context, $blocks);

    }


    /**
     * Fire the creator/composer events and return the modified context.
     *
     * @param $context  The old context
     * @return array    The new context
     */
    public function fireEvents($context){

        /** @var \Illuminate\View\Environment $env */
        $env  = $context['__env'];
        \View::callCreator($view = new View($env, $env->getEngineResolver()->resolve('twig'), $this->getTemplateName(), null, $context));
        \View::callComposer($view);

        return $view->getData();
    }

    /**
     * Determine wether events should fire for this View.
     *
     * @return bool
     */
    public function shouldFireEvents(){
        $name = $this->getTemplateName();
        //If a path is passed to Twig, events already have been fired.
        return !is_file($name);
    }

}
