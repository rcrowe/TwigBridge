<?php

namespace TwigBridge\Extensions;

use Twig_Function_Function;

class Example extends Extension
{
    public function getName()
    {
        return 'Example';
    }

    public function getFunctions()
    {
        return array(
            'example_url' => new Twig_Function_Function('URL::to'),
        );
    }
}