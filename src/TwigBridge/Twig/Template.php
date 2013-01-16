<?php

/**
 * Brings Twig to Laravel.
 *
 * @author Rob Crowe <hello@vivalacrowe.com>
 * @license MIT
 */

namespace TwigBridge\Twig;

use Twig_Template;
use Twig_TemplateInterface;

/**
 * This class is extended by the generated templates.
 */
abstract class Template extends Twig_Template
{
    /**
     * Returns the attribute value for a given array/object.
     *
     * Allows Eloquent results to work properly.
     */
    protected function getAttribute($object, $item, array $arguments = array(), $type = Twig_TemplateInterface::ANY_CALL, $isDefinedTest = false, $ignoreStrictCheck = false)
    {
        $ret = parent::getAttribute($object, $item, $arguments, $type, $isDefinedTest, $ignoreStrictCheck);

        // We need to handle relations differently when dealing with Eloquent objects
        if (is_a($ret, 'Illuminate\Database\Eloquent\Relations\Relation')) {

            // Grab the value from the relation
            $ret = $object->getAttribute($item);

            if ($ret AND $isDefinedTest) {
                return true;
            }
        }

        return $ret;
    }
}