<?php

namespace TwigBridge\Tests\Extension\Loader;

use TwigBridge\Tests\Base;
use Mockery as m;

class LoaderTest extends Base
{
    public function testDefault()
    {
        $loader = m::mock('TwigBridge\Extension\Loader\Loader');

        $this->assertEquals($loader->parseCallable('foo', null), [
            'foo',
            null,
            []
        ]);
    }

    public function testCallbackOption()
    {
        $loader = m::mock('TwigBridge\Extension\Loader\Loader');
        $parsed = $loader->parseCallable('foo', [
            'callback' => 'bar',
            'hello'    => 'world',
        ]);

        $this->assertEquals($parsed, [
            'foo',
            'bar',
            [
                'hello' => 'world'
            ]
        ]);
    }

    public function testNoCallback()
    {
        $loader = m::mock('TwigBridge\Extension\Loader\Loader');
        $parsed = $loader->parseCallable('foo', [
            'hello' => 'world',
        ]);

        $this->assertEquals($parsed, [
            'foo',
            'foo',
            [
                'hello' => 'world'
            ]
        ]);
    }

    public function testNumericMethod()
    {
        $loader = m::mock('TwigBridge\Extension\Loader\Loader');
        $parsed = $loader->parseCallable(1, 'bar');

        $this->assertEquals($parsed, [
            'bar',
            'bar',
            []
        ]);
    }

    public function testClassMethodCallable()
    {
        $loader = m::mock('TwigBridge\Extension\Loader\Loader');
        $parsed = $loader->parseCallable('foo', 'Bar@baz');

        $this->assertEquals($parsed, [
            'foo',
            [
                'Bar',
                'baz'
            ],
            []
        ]);
    }
}
