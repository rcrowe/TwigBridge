<?php

namespace TwigBridge\Tests\Bridge;

use Twig\Source;
use TwigBridge\Tests\Base;
use Mockery as m;
use TwigBridge\Bridge;

class LintTest extends Base
{
    public function tearDown(): void
    {
        m::close();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUnknownFile()
    {
        $this->expectException(\InvalidArgumentException::class);
        $finder = m::mock('TwigBridge\Twig\Loader');
        $bridge = new Bridge($finder);
        $app    = $this->getApplication();

        $finder->shouldReceive('getSourceContext')->andReturn(new Source('', 'testUnknownFile'));
        $app['twig.loader.viewfinder'] = $finder;

        $bridge->setApplication($app);
        $bridge->lint('test.twig');
    }

    public function testInvalidFile()
    {
        $finder = m::mock('TwigBridge\Twig\Loader');
        $bridge = new Bridge($finder);
        $app    = $this->getApplication();

        $finder->shouldReceive('getSourceContext')->andReturn(new \Twig\Source('{{ name }', 'test.twig'));
        $app['twig.loader.viewfinder'] = $finder;

        $bridge->setApplication($app);
        $this->assertFalse($bridge->lint('test.twig'));
    }

    public function testValidFile()
    {
        $finder = m::mock('TwigBridge\Twig\Loader');
        $bridge = new Bridge($finder);
        $app    = $this->getApplication();

        $finder->shouldReceive('getSourceContext')->andReturn(new \Twig\Source('{{ name }}', 'test.twig'));
        $app['twig.loader.viewfinder'] = $finder;

        $bridge->setApplication($app);
        $this->assertTrue($bridge->lint('test.twig'));
    }
}
