<?php

namespace TwigBridge\Tests\Bridge;

use TwigBridge\Tests\Base;
use Mockery as m;
use TwigBridge\Bridge;

class LintTest extends Base
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testUnknownFile()
    {
        $bridge = new Bridge;
        $app    = $this->getApplication();

        $finder = m::mock('TwigBridge\Twig\Loader');
        $finder->shouldReceive('getSource')->andReturn(false);
        $app['twig.loader.viewfinder'] = $finder;

        $bridge->setApplication($app);
        $bridge->lint('test.twig');
    }

    public function testInvalidFile()
    {
        $bridge = new Bridge;
        $app    = $this->getApplication();

        $finder = m::mock('TwigBridge\Twig\Loader');
        $finder->shouldReceive('getSource')->andReturn('{{ name }');
        $app['twig.loader.viewfinder'] = $finder;

        $bridge->setApplication($app);
        $this->assertFalse($bridge->lint('test.twig'));
    }

    public function testValidFile()
    {
        $bridge = new Bridge;
        $app    = $this->getApplication();

        $finder = m::mock('TwigBridge\Twig\Loader');
        $finder->shouldReceive('getSource')->andReturn('{{ name }}');
        $app['twig.loader.viewfinder'] = $finder;

        $bridge->setApplication($app);
        $this->assertTrue($bridge->lint('test.twig'));
    }
}
