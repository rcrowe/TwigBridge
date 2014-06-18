<?php

namespace TwigBridge\Tests\Extension\Loader;

use TwigBridge\Tests\Base;
use Mockery as m;
use TwigBridge\Extension\Loader\Facades;

class FacadesTest extends Base
{
    public function testName()
    {
        $facades = new Facades(m::mock('Illuminate\Config\Repository'));

        $this->assertInternalType('string', $facades->getName());
    }

    public function testNoGlobals()
    {
        $config = m::mock('Illuminate\Config\Repository');
        $config->shouldReceive('get')->andReturn([]);

        $facades = new Facades($config);
        $globals = $facades->getGlobals();

        $this->assertInternalType('array', $globals);
        $this->assertTrue(empty($globals));
    }

    public function testAddGlobal()
    {
        $config = m::mock('Illuminate\Config\Repository');
        $config->shouldReceive('get')->andReturn([
            'foo' => 'bar',
        ]);

        $facades = new Facades($config);
        $global  = $facades->getGlobals()['foo'];

        $this->assertInstanceOf('TwigBridge\Extension\Loader\Facade\Caller', $global);
    }
}
