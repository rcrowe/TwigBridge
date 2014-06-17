<?php

namespace TwigBridge\Tests\Command\Lint;

use Mockery as m;
use Symfony\Component\Console\Output\StreamOutput;
use TwigBridge\Command\Lint;

class BasicTest extends Base
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

        $input  = new \Symfony\Component\Console\Input\ArrayInput([]);
        $output = m::mock('Symfony\Component\Console\Output\OutputInterface');
        $output->shouldReceive('isVerbose')->andReturn(StreamOutput::VERBOSITY_QUIET);
        $output->shouldReceive('writeln')->with('<comment>0/0 valid files</comment>');

        $command->run($input, $output);
    }

    public function testEmptyJSON()
    {
        $command = new Lint;
        $app     = $this->getApplication();
    
        $command->setLaravel($app);

        $input  = new \Symfony\Component\Console\Input\ArrayInput([
            '--format' => 'json'
        ]);
        $output = m::mock('Symfony\Component\Console\Output\OutputInterface');
        $output->shouldReceive('isVerbose')->andReturn(StreamOutput::VERBOSITY_QUIET);
        $output->shouldReceive('writeln')->with("[]");

        $command->run($input, $output);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidFormat()
    {
        $command = new Lint;
        $app     = $this->getApplication();
    
        $command->setLaravel($app);

        $input  = new \Symfony\Component\Console\Input\ArrayInput([
            '--format' => 'foo'
        ]);
        $output = m::mock('Symfony\Component\Console\Output\OutputInterface');
        $output->shouldReceive('isVerbose')->andReturn(StreamOutput::VERBOSITY_QUIET);

        $command->run($input, $output);
    }
}
