<?php

namespace TwigBridge\Tests\ServiceProvider\Bindings;

use TwigBridge\Tests\Base;
use Mockery as m;
use TwigBridge\ServiceProvider;

class BridgeTest extends Base
{
    public function testInstance()
    {
        $app                    = $this->getApplication();
        $app['twig.extensions'] = [];

        $provider = new ServiceProvider($app);
        $provider->register();
        $provider->boot();

        $this->assertInstanceOf('TwigBridge\Bridge', $app['twig']);
    }

    public function testSetLexer()
    {
        $app = $this->getApplication();
        $app['twig.lexer'] = m::mock('Twig_Lexer');
        $app['twig.lexer']->shouldReceive('fooBar')->andReturn('baz');
        $app['twig.extensions'] = [];

        $provider = new ServiceProvider($app);
        $provider->register();
        $provider->boot();

        $this->assertEquals($this->readAttribute($app['twig'], 'lexer')->fooBar(), 'baz');
    }

    public function testAddExtensions()
    {
        $app                    = $this->getApplication();
        $app['twig.extensions'] = [];
        $provider               = new ServiceProvider($app);
        $provider->register();
        $provider->boot();

        $called = false;
        $app->resolving('twig.extensions', function () use (&$called) {
            $called = true;
        });

        $app['twig'];
        $this->assertTrue($called);
    }
}
