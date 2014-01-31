<?php

namespace TwigBridgeTests\View;

use PHPUnit_Framework_TestCase;
use Mockery as m;

use Illuminate\View\Environment;
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

        $environment = new Environment(
            m::mock('Illuminate\View\Engines\EngineResolver'),
            m::mock('Illuminate\View\ViewFinderInterface'),
            $dispatcher
        );

        $engine = $this->getEngine();
        $view   = new View($environment, $engine, 'base.twig', __DIR__);

        $view->render();

        // Grab globals set on the Twig environment
        $globals = $engine->getTwig()->getGlobals();

        $this->assertArrayHasKey('__env', $globals);
        $this->assertInstanceOf('Illuminate\View\Environment', $globals['__env']);
    }

    private function getFilesystem()
    {
        $finder = m::mock('Illuminate\View\ViewFinderInterface');
        $finder->shouldReceive('getHints')->andReturn([]);
        $finder->shouldReceive('getPaths')->andReturn([
            __DIR__.'/../fixtures/Filesystem'
        ]);

        return new Filesystem($finder);
    }

    private function getEngine()
    {
        $twig = new Twig_Environment($this->getFilesystem(), array());

        return new TwigEngine($twig, []);
    }
}
