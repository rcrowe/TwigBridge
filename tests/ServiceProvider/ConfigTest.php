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
        $dir = realpath(__DIR__.'/../../src').'/Config';

        var_dump(__DIR__);
        var_dump($dir);

        $app['config']->getLoader()->shouldReceive('addNamespace')->with('twigbridge', $dir)->once();

        $provider = new TwigServiceProvider($app);
        $provider->register();
    }
}
