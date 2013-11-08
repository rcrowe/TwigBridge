<?php

namespace TwigBridgeTests\ServiceProvider;

use TwigBridgeTests\Base;
use TwigBridge\TwigServiceProvider;

class ConfigTest extends Base
{
    public function testConfigPath()
    {
        $app = $this->getApplication();

        // Check that our register is registering our config path correctly
        $dir = realpath(__DIR__.'/../../../').'/src/TwigBridge/../config';
        $app['config']->getLoader()->shouldReceive('addNamespace')->with('twigbridge', $dir)->once();

        $provider = new TwigServiceProvider($app);
        $provider->register();
    }
}
