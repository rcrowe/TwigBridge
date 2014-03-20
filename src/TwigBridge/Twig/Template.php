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
    public function display(array $context, array $blocks = array())
    {
        if ($this->shouldFireEvents()) {
            $context = $this->fireEvents($context);
        }

        parent::display($context, $blocks);
    }

    /**
     * Fire the creator/composer events and return the modified context.
     *
     * @param $context Old context.
     *
     * @return array New context.
     */
    public function fireEvents($context)
    {
        $env  = $context['__env'];
        $view = new View($env, $env->getEngineResolver()->resolve('twig'), $this->getTemplateName(), null, $context);

        $env->callCreator($view);
        $env->callComposer($view);

        return $view->getData();
    }

    /**
     * Determine whether events should fire for this view.
     *
     * @return bool
     */
    public function shouldFireEvents()
    {
        $name = $this->getTemplateName();

        // If a path is passed to Twig, events have been fired already.
        return !is_file($name);
    }

    /**
     * {@inheritdoc}
     */
    protected function getAttribute(
        $object,
        $item,
        array $arguments = array(),
        $type = Twig_Template::ANY_CALL,
        $isDefinedTest = false,
        $ignoreStrictCheck = false
    ) {
        if ($type !== Twig_Template::METHOD_CALL && is_a($object, 'Illuminate\Database\Eloquent\Model')) {
            return $object->getAttribute($item);
        } else {
            return parent::getAttribute($object, $item, $arguments, $type, $isDefinedTest, $ignoreStrictCheck);
        }
    }
}
