<?php

namespace TwigBridge\Tests\Extension\Laravel;

use TwigBridge\Tests\Base;
use Mockery as m;
use TwigBridge\Extension\Laravel\Config;

class ConfigTest extends Base
{
    public function tearDown(): void
    {
        m::close();
    }

    public function testName()
    {
        $this->assertInternalType('string', $this->getConfig()->getName());
    }

    public function testFunctions()
    {
        $config    = $this->getConfig();
        $functions = $config->getFunctions();

        $this->assertInternalType('array', $functions);

        foreach ($functions as $function) {
            $this->assertInstanceOf('Illuminate\Config\Repository', $function->getCallable()[0]);
        }
    }

    protected function getConfig()
    {
        return new Config(m::mock('Illuminate\Config\Repository'));
    }
}
