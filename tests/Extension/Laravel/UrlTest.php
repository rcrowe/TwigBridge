<?php

namespace TwigBridge\Tests\Extension\Laravel;

use TwigBridge\Tests\Base;
use Mockery as m;
use TwigBridge\Extension\Laravel\Url;

class UrlTest extends Base
{
    protected $customFunctions = [
        'asset',
        'action',
        'route',
        'secure_url',
        'secure_asset',
    ];

    public function tearDown()
    {
        m::close();
    }

    public function testName()
    {
        $this->assertInternalType('string', $this->getUrl()->getName());
    }

    public function testCustomFunctions()
    {
        $url       = $this->getUrl();
        $functions = $url->getFunctions();

        $this->assertInternalType('array', $functions);

        foreach ($functions as $function) {
            if (!in_array($function->getName(), $this->customFunctions)) {
                continue;
            }

            $this->assertInstanceOf('Illuminate\Routing\UrlGenerator', $function->getCallable()[0]);
        }
    }

    public function testUrl()
    {
        $url       = $this->getUrl();
        $functions = $url->getFunctions();

        foreach ($functions as $function) {
            if ($function->getName() !== 'url') {
                continue;
            }

            $callable = $function->getCallable();

            $this->assertInstanceOf('TwigBridge\Extension\Laravel\Url', $callable[0]);
            $this->assertEquals('url', $callable[1]);
        }
    }

    public function testWildcardFunctions()
    {
        $generator = m::mock('Illuminate\Routing\UrlGenerator');
        $generator->shouldReceive('to')->once();
        $generator->shouldReceive('fooBar')->once();

        $dispatcher = m::mock('Illuminate\Contracts\Events\Dispatcher');
        $url = new Url($generator, m::mock('Illuminate\Routing\Router', [ $dispatcher ])->makePartial());
        $functions = $url->getFunctions();

        foreach ($functions as $function) {
            if (in_array($function->getName(), $this->customFunctions)) {
                continue;
            }

            call_user_func($function->getCallable(), 'foo_bar');
        }
    }

    public function testIsSafe()
    {
        $url       = $this->getUrl();
        $functions = $url->getFunctions();
        $mock      = m::mock('Twig_Node');

        foreach ($functions as $function) {
            if (is_a($function->getCallable(), 'Closure')) {
                continue;
            }

            $this->assertTrue(in_array('html', $function->getSafe($mock)));
        }
    }

    protected function getUrl()
    {
        return new Url(m::mock('Illuminate\Routing\UrlGenerator'), m::mock('Illuminate\Routing\Router'));
    }
}
