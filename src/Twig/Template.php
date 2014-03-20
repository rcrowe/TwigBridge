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
        // We need to handle accessing attributes/methods on an Eloquent instance differently
        if (is_a($object, 'Illuminate\Database\Eloquent\Model')) {
            if (method_exists($object, $item)) {
                $ret = call_user_func_array(array($object, $item), $arguments);
            } else {
                // Calling getAttributes lets us deal with accessors, mutators & relations
                $ret = $object->getAttribute($item);

                // getAttributes doesn't deal with attributes that aren't part of the models data
                if ($ret === null && isset($object->$item)) {
                    $ret = $object->$item;
                }
            }
        } else {
            $ret = parent::getAttribute($object, $item, $arguments, $type, $isDefinedTest, $ignoreStrictCheck);
        }

        // We need to handle relations differently when dealing with Eloquent objects
        if (is_a($ret, 'Illuminate\Database\Eloquent\Relations\Relation')) {
            // Grab the value from the relation
            $ret = $object->getAttribute($item);
        }

        if ($ret && $isDefinedTest) {
            return true;
        }

        return $ret;
    }
}
