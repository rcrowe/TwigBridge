<?php

namespace TwigBridge;

use Twig_Template;
use Twig_TemplateInterface;

abstract class EloquentTemplate extends Twig_Template
{
    protected function getAttribute($object, $item, array $arguments = array(), $type = Twig_TemplateInterface::ANY_CALL, $isDefinedTest = false, $ignoreStrictCheck = false)
	{
		$ret = parent::getAttribute($object, $item, $arguments, $type, $isDefinedTest, $ignoreStrictCheck);
		if (is_a($ret, 'Illuminate\Database\Eloquent\Relations\Relation'))
		{
			// we got a Laravel relation back...this isn't what we wanted.  Better get the right value...
            $ret = $object->getAttribute($item);
            if ($ret) {
                if ($isDefinedTest) {
                    return true;
                }
            }
		}
		return $ret;
	}
	

}