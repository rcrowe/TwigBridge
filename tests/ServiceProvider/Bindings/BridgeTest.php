<?php

namespace TwigBridge\Tests\ServiceProvider\Bindings;

use Mockery as m;
use Twig\Lexer;
use TwigBridge\ServiceProvider;
use TwigBridge\Tests\Base;

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
        $app['twig.lexer'] = m::mock(Lexer::class);
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
