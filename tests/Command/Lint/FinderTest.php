<?php

namespace TwigBridge\Tests\Command\Lint;

use Mockery as m;
use TwigBridge\Command\Lint;

class FinderTest extends Base
{
    public function testGet()
    {
        $app     = $this->getApplication();
        $command = new Lint;
        $command->setLaravel($app);
        $this->assertInstanceOf('Symfony\Component\Finder\Finder', $command->getFinder([__DIR__]));
    }

    public function testSet()
    {
        /** @var \Symfony\Component\Finder\Finder|\Mockery\MockInterface $finder */
        $finder = m::mock('Symfony\Component\Finder\Finder');
        $finder->shouldReceive('files')->andReturn($finder);
        $finder->shouldReceive('in')->andReturn($finder);
        $finder->shouldReceive('name')->andReturn($finder);
        $finder->shouldReceive('count')->andReturn(1);

        $app     = $this->getApplication();
        $command = new Lint;
        $command->setLaravel($app);
        $command->setFinder($finder);

        $this->assertEquals(1, $command->getFinder([__DIR__])->count());
    }
}
