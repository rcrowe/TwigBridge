<?php

namespace TwigBridge\Tests\Command\Lint;

use Mockery as m;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use TwigBridge\Command\Lint;

class FormatTest extends Base
{
    public function testInstance()
    {
        $command = new Lint;

        $this->assertInstanceOf('Illuminate\Console\Command', $command);
    }

    public function testEmpty()
    {
        $command = new Lint;
        $app     = $this->getApplication();

        $command->setLaravel($app);

        $finder = m::mock('Symfony\Component\Finder\Finder');
        $finder->shouldReceive('files')->andReturn($finder);
        $finder->shouldReceive('in')->andReturn($finder);
        $finder->shouldReceive('name')->andReturn([]);
        $command->setFinder($finder);

        $input  = new ArrayInput([]);
        $output = m::mock('Symfony\Component\Console\Output\NullOutput')->makePartial();
        $output->shouldReceive('writeln')->with('<comment>0/0 valid files</comment>');

        $command->run($input, $output);
    }

    public function testEmptyJSON()
    {
        $command = new Lint;
        $app     = $this->getApplication();

        $command->setLaravel($app);

        $finder = m::mock('Symfony\Component\Finder\Finder');
        $finder->shouldReceive('files')->andReturn($finder);
        $finder->shouldReceive('in')->andReturn($finder);
        $finder->shouldReceive('name')->andReturn([]);
        $command->setFinder($finder);

        $input  = new ArrayInput([
            '--format' => 'json'
        ]);
        $output = m::mock('Symfony\Component\Console\Output\NullOutput')->makePartial();
        $output->shouldReceive('writeln')->with("[]");

        $command->run($input, $output);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidFormat()
    {
        $this->expectException(\InvalidArgumentException::class);

        $command = new Lint;
        $app     = $this->getApplication();

        $command->setLaravel($app);

        $finder = m::mock('Symfony\Component\Finder\Finder');
        $finder->shouldReceive('files')->andReturn($finder);
        $finder->shouldReceive('in')->andReturn($finder);
        $finder->shouldReceive('name')->andReturn($finder);
        $command->setFinder($finder);

        $input  = new ArrayInput([
            '--format' => 'foo'
        ]);
        $output = m::mock('Symfony\Component\Console\Output\NullOutput')->makePartial();

        $command->run($input, $output);
    }
}
