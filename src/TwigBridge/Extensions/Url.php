<?php

namespace TwigBridge\Extensions;

use Twig_Extension;
use Twig_Function_Function;

class Url extends Twig_Extension
{
    public function getName()
    {
        return 'url';
    }

    public function getFunctions()
    {
        return array(
            'url' => new Twig_Function_Function('URL::to'),
        );
    }
}