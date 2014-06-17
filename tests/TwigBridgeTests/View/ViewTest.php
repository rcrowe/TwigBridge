<?php

namespace TwigBridgeTests\View;

use PHPUnit_Framework_TestCase;
use Mockery as m;
use Illuminate\View\Factory;
use TwigBridge\View\View;
use TwigBridge\Engines\TwigEngine;
use TwigBridge\Twig\Loader\Filesystem;
use Twig_Environment;

class ViewTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testViewGlobals()
    {
        $dispatcher = m::mock('Illuminate\Events\Dispatcher');
        $dispatcher->shouldReceive('fire')->andReturn(true);

        $factory = new Factory(
            m::mock('Illuminate\View\Engines\EngineResolver'),
            m::mock('Illuminate\View\ViewFinderInterface'),
            $dispatcher
        );

        $engine = $this->getEngine();
        $view   = new View($factory, $engine, 'base.twig', __DIR__);

        $view->render();

        // Grab globals set on the Twig environment
        $globals = $engine->getTwig()->getGlobals();

        $this->assertArrayHasKey('__env', $globals);
        $this->assertInstanceOf('Illuminate\View\Factory', $globals['__env']);
    }

    private function getFilesystem()
    {
        $finder = m::mock('Illuminate\View\ViewFinderInterface');
        $finder->shouldReceive('getHints')->andReturn(array());
        $finder->shouldReceive('getPaths')->andReturn(array(
            __DIR__.'/../fixtures/Filesystem'
        ));

        return new Filesystem($finder);
    }

    private function getEngine()
    {
        $twig = new Twig_Environment($this->getFilesystem(), array());

        return new TwigEngine($twig, array());
    }
}
