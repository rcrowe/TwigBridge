<?php

namespace TwigBridge\Tests\ServiceProvider;

use TwigBridge\Tests\Base;
use TwigBridge\TwigServiceProvider;

class ConfigTest extends Base
{
    public function testConfigPath()
    {
        $app = $this->getApplication();

        // Check that our register is registering our config path correctly
        $dir = $this->twigBridgeRoot.'/Config';
        $app['config']->getLoader()->shouldReceive('addNamespace')->with('twigbridge', $dir)->once();

        $provider = new TwigServiceProvider($app);
        $provider->register();
    }
}
