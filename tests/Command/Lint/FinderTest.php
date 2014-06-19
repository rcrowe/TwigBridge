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
        $this->assertInstanceOf('Symfony\Component\Finder\Finder', (new Lint)->getFinder([__DIR__]));
    }

    public function testSet()
    {
        $data = ['fooBar'];

        $finder = m::mock('Symfony\Component\Finder\Finder');
        $finder->shouldReceive('files')->andReturn($finder);
        $finder->shouldReceive('in')->andReturn($finder);
        $finder->shouldReceive('name')->andReturn($data);

        $command = new Lint;
        $command->setFinder($finder);
        $command->setFinder($finder);

        $this->assertEquals($data, $command->getFinder([__DIR__]));
    }
}
