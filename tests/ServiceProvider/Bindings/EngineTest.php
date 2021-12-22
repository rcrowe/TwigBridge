<?php

namespace TwigBridge\Tests\ServiceProvider\Bindings;

use TwigBridge\Tests\Base;
use TwigBridge\ServiceProvider;
use Mockery as m;

class EngineTest extends Base
{
    public function tearDown(): void
    {
        m::close();
    }

    public function testEngineExtension()
    {
        $app      = $this->getApplication();
        $provider = new ServiceProvider($app);
        $provider->register();
        $provider->boot();

        // Resolving the engine will force all extensions to be loaded
        // Extensions resolve their dependencies out of the IoC, and we don't
        // want to mock all of those.
        $app['twig.extensions'] = [];

        $this->assertInstanceOf('TwigBridge\Engine\Twig', $app['view']->getEngineResolver()->resolve('twig'));
    }

    public function testCallBridge()
    {
        $app                    = $this->getApplication();
        $app['twig.extensions'] = [];
        $provider               = new ServiceProvider($app);
        $provider->register();
        $provider->boot();

        $called = false;
        $app->resolving('twig', function () use (&$called) {
            $called = true;
        });

        $app['view']->getEngineResolver()->resolve('twig');

        $this->assertTrue($called);
    }
}
