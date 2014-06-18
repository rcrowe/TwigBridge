<?php

namespace TwigBridge\Tests\ServiceProvider\Bindings;

use TwigBridge\Tests\Base;
use TwigBridge\ServiceProvider;
use Mockery as m;

class EngineTest extends Base
{
    public function tearDown()
    {
        m::close();
    }

    public function testEngineExtension()
    {
        $app      = $this->getApplication();
        $provider = new ServiceProvider($app);
        $provider->boot();

        $this->assertInstanceOf('TwigBridge\Engine\Twig', $app['view']->getEngineResolver()->resolve('twig'));
    }

    public function testCallBridge()
    {
        $app      = $this->getApplication();
        $provider = new ServiceProvider($app);
        $provider->boot();

        $called = false;
        $app->resolving('twig.bridge', function () use (&$called) {
            $called = true;
        });

        $app['view']->getEngineResolver()->resolve('twig');

        $this->assertTrue($called);
    }
}
