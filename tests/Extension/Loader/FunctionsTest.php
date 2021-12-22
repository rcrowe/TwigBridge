<?php

namespace TwigBridge\Tests\Extension\Loader;

use TwigBridge\Tests\Base;
use Mockery as m;
use TwigBridge\Extension\Loader\Functions;

class FunctionsTest extends Base
{
    public function testName()
    {
        $functions = new Functions(m::mock('Illuminate\Config\Repository'));

        $this->assertTrue(is_string($functions->getName()));
    }

    public function testNoFunctions()
    {
        $config = m::mock('Illuminate\Config\Repository');
        $config->shouldReceive('get')->andReturn([]);

        $functions = new Functions($config);
        $functions = $functions->getFunctions();

        $this->assertTrue(is_array($functions));
        $this->assertTrue(empty($functions));
    }

    public function testAddFunction()
    {
        $config = m::mock('Illuminate\Config\Repository');
        $config->shouldReceive('get')->andReturn([
            'foo' => 'bar',
            'Baz' => function () {
                return 'bing';
            }
        ]);

        $functions = new Functions($config);
        $function  = $functions->getFunctions()[1];

        $this->assertEquals('bing', call_user_func($function->getCallable()));
    }
}
