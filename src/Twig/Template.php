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
        if (!isset($context['__env'])) {
            $context = $this->env->mergeShared($context);
        }

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
    )
    {
        // we need a reflection to identify static methods, which are outside laravel's model
        if( method_exists( $object, $item ) )
            $reflection = new \ReflectionMethod($object, $item);
        else
            $reflection = null;
        // seek out eloquent models
        if(
            !($reflection && $reflection->isStatic())
            && is_a( $object, 'Illuminate\Database\Eloquent\Model' )
        )
        {
            // load all relations from the eloquent model
            $relations = $object->getRelations();
            // relation called as method
            if( array_get( $relations, $item, false ) && $type == Twig_Template::METHOD_CALL )
                return $object->$item();
            // if called as normal property
            if( array_get( $relations, $item, false ) )
                return $object->$item;
            // if this is a true method, call it
            if( $reflection && $reflection->isPublic() )
                return $reflection->invokeArgs( $object, $arguments );
            // otherwise use the build in model handler
            return $object->getAttribute( $item );
        } else
        {
            return parent::getAttribute( $object, $item, $arguments, $type, $isDefinedTest, $ignoreStrictCheck );
        }
    }
}
