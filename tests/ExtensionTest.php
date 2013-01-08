<?php

use TwigBridge\Extension;
use Illuminate\Foundation\Application;

class mockExtension extends Extension
{
    public function getName()
    {
        return 'mockExtension';
    }
}

class ExtensionTest extends PHPUnit_Framework_TestCase
{
    public function testExtension()
    {
        $app  = new Application;
        $twig = new Twig_Environment;

        $extension = new mockExtension($app, $twig);

        $this->assertEquals(get_class($extension->getApp()), 'Illuminate\Foundation\Application');
        $this->assertEquals(get_class($extension->getTwig()), 'Twig_Environment');
    }
}