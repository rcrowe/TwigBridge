<?php

namespace TwigBridge\Tests\Bridge;

use TwigBridge\Tests\Base;
use Mockery as m;
use TwigBridge\Bridge;
use Twig_Environment;

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
        $app    = $this->getApplication();
        $bridge = new Bridge($app);

        $app['twig'] = new Twig_Environment;

        $finder = m::mock('TwigBridge\Twig\Loader\Viewfinder');
        $finder->shouldReceive('getSource')->andReturn(false);
        $app['twig.loader.viewfinder'] = $finder;

        $bridge->lint('test.twig');
    }

    public function testInvalidFile()
    {
        $app    = $this->getApplication();
        $bridge = new Bridge($app);

        $app['twig'] = new Twig_Environment;

        $finder = m::mock('TwigBridge\Twig\Loader\Viewfinder');
        $finder->shouldReceive('getSource')->andReturn('{{ name }');
        $app['twig.loader.viewfinder'] = $finder;

        $this->assertFalse($bridge->lint('test.twig'));
    }

    public function testValidFile()
    {
        $app    = $this->getApplication();
        $bridge = new Bridge($app);

        $app['twig'] = new Twig_Environment;

        $finder = m::mock('TwigBridge\Twig\Loader\Viewfinder');
        $finder->shouldReceive('getSource')->andReturn('{{ name }}');
        $app['twig.loader.viewfinder'] = $finder;

        $this->assertTrue($bridge->lint('test.twig'));
    }
}
