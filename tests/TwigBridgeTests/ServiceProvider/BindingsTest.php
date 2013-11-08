<?php

namespace TwigBridgeTests\ServiceProvider;

use TwigBridgeTests\Base;
use TwigBridge\TwigServiceProvider;

class BindingsTest extends Base
{
    public function testBindings()
    {
        $bindings = array(
            'twig.extensions',
            'twig.loader.path',
            'twig.loader.viewfinder',
            'twig.loader.filesystem',
            'twig.loader',
            'twig.options',
            'twig.bridge',
            'twig',
            'twig.engine',
        );

        $app      = $this->getApplication();
        $provider = new TwigServiceProvider($app);

        // Make sure not found
        foreach ($bindings as $binding) {
            $this->assertFalse($app->bound($binding));
        }

        // Boot provider
        $provider->boot();

        // Now make sure bounded
        foreach ($bindings as $binding) {
            $this->assertTrue($app->bound($binding));
        }
    }

    public function testTwigOptions()
    {
        $app      = $this->getApplication();
        $provider = new TwigServiceProvider($app);
        $provider->boot();

        $this->assertSame($app['twig.options'], $app['config']->get('twigbridge::twig'));
    }

    public function testTwigExtensions()
    {
        $app      = $this->getApplication();
        $provider = new TwigServiceProvider($app);
        $provider->boot();

        $this->assertSame($app['twig.extensions'], $app['config']->get('twigbridge::extensions.enabled'));
    }

    public function testTwigExtensionsWithDebug()
    {
        $app = $this->getApplication(array(
            'twig' => array(
                'debug' => true
            )
        ));

        $provider = new TwigServiceProvider($app);
        $provider->boot();

        $this->assertSame($app['twig.extensions'][0], 'Twig_Extension_Debug');
    }
}
