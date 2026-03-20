<?php

namespace TwigBridge\Tests;

class Helpers
{
    public static function readObjectAttribute(object $object, string $attributeName)
    {
        $reflector = new \ReflectionObject($object);

        do {
            try {
                $attribute = $reflector->getProperty($attributeName);

                if (!$attribute || $attribute->isPublic()) {
                    return $object->$attributeName;
                }

                return $attribute->getValue($object);
            } catch (\ReflectionException $e) {
                // do nothing
            }
        } while ($reflector = $reflector->getParentClass());

        throw new \Exception(
            sprintf('Attribute "%s" not found in object.', $attributeName)
        );
    }
}
