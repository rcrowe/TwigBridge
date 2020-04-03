<?php


namespace TwigBridge\Extension\Loader;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

/**
 * Add 'app' and all global variables shared through View::share
 */
class Globals extends AbstractExtension implements GlobalsInterface
{

    public function getGlobals(): array
    {
        $globals = app('view')->getShared();
        $globals['app'] = app();
        return $globals;
    }
}