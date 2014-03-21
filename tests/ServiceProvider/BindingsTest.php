<?php

namespace TwigBridge\Tests\ServiceProvider;

use TwigBridge\Tests\Base;
use TwigBridge\ServiceProvider;

class BindingsTest extends Base
{
    public function testBindings()
    {
        $app      = $this->getApplication();
        $provider = new ServiceProvider($app);

        // Make sure not found
        foreach ($provider->provides() as $binding) {
            $this->assertFalse($app->bound($binding));
        }

        // Boot provider
        $provider->boot();

        // Now make sure bounded
        foreach ($provider->provides() as $binding) {
            $this->assertTrue($app->bound($binding));
        }
    }
}
