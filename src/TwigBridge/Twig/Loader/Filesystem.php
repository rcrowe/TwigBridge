<?php

namespace TwigBridge\Twig\Loader;

use Twig_Loader_Filesystem;

class Filesystem extends Twig_Loader_Filesystem
{
    /**
     * No longer have to use an extension for a twig file in your templates.
     */
    protected function findTemplate($name)
    {
        // Append twig file extension
        // Make things nicer in the templates
        if (strtolower(substr($name, -5)) !== '.twig') {
            $name .= '.twig';
        }

        return parent::findTemplate($name);
    }
}