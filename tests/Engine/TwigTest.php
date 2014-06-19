<?php

namespace TwigBridge\Tests\Engine;

use TwigBridge\Tests\Base;
use Mockery as m;
use TwigBridge\Engine\Twig as Engine;
use TwigBridge\Engine\Compiler;
use Twig_Environment;
use Twig_Loader_Array;
use InvalidArgumentException;

class TwigTest extends Base
{
    public function tearDown()
    {
        m::close();
    }

    public function testInstance()
    {
        $global   = array('name' => 'Rob');
        $compiler = new Compiler(new Twig_Environment);
        $engine   = new Engine($compiler, $global);

        $this->assertInstanceOf('Illuminate\View\Engines\CompilerEngine', $engine);
        $this->assertEquals($global, $engine->getGlobalData());
    }

    public function testSetGlobalData()
    {
        $global   = array('package' => 'TwigBridge');
        $compiler = new Compiler(new Twig_Environment);
        $engine   = new Engine($compiler);

        $engine->setGlobalData($global);

        $this->assertEquals($global, $engine->getGlobalData());
    }

    public function testGet()
    {
        $path = __DIR__;
        $data = ['foo' => 'bar'];

        $template = m::mock('TwigBridge\Twig\Template');
        $template->shouldReceive('render')->once()->with($data);

        $compiler = m::mock('TwigBridge\Engine\Compiler');
        $compiler->shouldReceive('load')->once()->with($path)->andReturn($template);

        $engine = new Engine($compiler);
        $engine->get($path, $data);
    }

    public function testGetWithGlobalData()
    {
        $path       = __DIR__;
        $globalData = ['package' => 'TwigBridge', 'foo' => 'baz'];
        $data       = ['foo' => 'bar'];

        $template = m::mock('TwigBridge\Twig\Template');
        $template->shouldReceive('render')->once()->with(array_merge($globalData, $data));

        $compiler = m::mock('TwigBridge\Engine\Compiler');
        $compiler->shouldReceive('load')->once()->with($path)->andReturn($template);

        $engine = new Engine($compiler, $globalData);
        $engine->get($path, $data);
    }
}
