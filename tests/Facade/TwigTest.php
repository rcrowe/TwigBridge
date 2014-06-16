<?php

namespace TwigBridge\Tests\Facade;

use TwigBridge\Tests\Base;
use TwigBridge\ServiceProvider;
use TwigBridge\Facade\Twig;

class TwigTest extends Base
{
    public function testFacadeInstance()
    {
        $this->bootApplication();

        $this->assertInstanceOf('TwigBridge\Bridge', Twig::getFacadeRoot());
    }

    protected function bootApplication()
    {
        $app = $this->getApplication();
        $app['config']->getLoader()->shouldReceive('addNamespace');

        $provider = new ServiceProvider($app);
        $provider->register();
        $provider->boot();

        Twig::setFacadeApplication($app);

        return $app;
    }
}
