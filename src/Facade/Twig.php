<?php

namespace TwigBridge\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Twig\Environment
 * @see \TwigBridge\Bridge
 */
class Twig extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'twig';
    }
}
