<?php

use TwigBridge\Engines\TwigEngine;
use Twig_Environment;
use TwigBridge\Twig\Loader\Filesystem;

class TwigEngineTest extends PHPUnit_Framework_TestCase
{
    public function testGetTwig()
    {
        $loader = new Filesystem(array(), 'twig');
        $twig   = new Twig_Environment($loader, array());

        $engine = new TwigEngine($twig);

        $this->assertEquals(get_class($engine->getTwig()), 'Twig_Environment');
    }

    public function testGetFile()
    {
        $loader = new Filesystem(array(__DIR__.'/../fixtures/'));
        $twig   = new Twig_Environment($loader, array());

        $engine = new TwigEngine($twig);

        $html = <<<HTML
<h1>Hello Rob</h1>

I am the child

HTML;

        $this->assertEquals($engine->get('child', array('name' => 'Rob')), $html);
    }

    public function testGlobalConfigData()
    {

    }
}