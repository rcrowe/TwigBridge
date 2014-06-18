<?php

/**
 * This file is part of the TwigBridge package.
 *
 * @copyright Robert Crowe <hello@vivalacrowe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
     * @var bool Have the creator/composer events fired.
     */
    protected $firedEvents = false;

    /**
     * {@inheritdoc}
     */
    public function display(array $context, array $blocks = [])
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
     * @return array New context if __env is passed in, else the passed in context is returned.
     */
    public function fireEvents($context)
    {
        if (!isset($context['__env'])) {
            return $context;
        }

        $env  = $context['__env'];
        $view = new View(
            $env,
            $env->getEngineResolver()->resolve('twig'),
            $this->getTemplateName(),
            null,
            $context
        );

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
        return !$this->firedEvents;
    }

    /**
     * Set the firedEvents flag, to make sure composers/creators only fire once.
     *
     * @param bool $fired
     *
     * @return void
     */
    public function setFiredEvents($fired = true)
    {
        $this->firedEvents = $fired;
    }

    /**
     * {@inheritdoc}
     */
    protected function getAttribute(
        $object,
        $item,
        array $arguments = [],
        $type = Twig_Template::ANY_CALL,
        $isDefinedTest = false,
        $ignoreStrictCheck = false
    ) {
        // We need to handle accessing attributes/methods on an Eloquent instance differently
        if (is_a($object, 'Illuminate\Database\Eloquent\Model')) {
            if (method_exists($object, $item)) {
                $ret = call_user_func_array([$object, $item], $arguments);
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
