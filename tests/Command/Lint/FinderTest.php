<?php

namespace TwigBridge\Tests\Command\Lint;

use Mockery as m;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
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
        $data = ['fooBar'];

        $finder = m::mock('Symfony\Component\Finder\Finder');
        $finder->shouldReceive('files')->andReturn($finder);
        $finder->shouldReceive('in')->andReturn($finder);
        $finder->shouldReceive('name')->andReturn($data);

        $app     = $this->getApplication();
        $command = new Lint;
        $command->setLaravel($app);
        $command->setFinder($finder);
        $command->setFinder($finder);

        $this->assertEquals($data, $command->getFinder([__DIR__]));
    }
}
