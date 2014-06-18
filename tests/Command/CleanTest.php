<?php

namespace TwigBridge\Tests\Command;

use TwigBridge\Tests\Base;
use Mockery as m;
use TwigBridge\Command\Clean;
use Symfony\Component\Console\Input\ArrayInput;

class CleanTest extends Base
{
    public function testInstance()
    {
        $command = new Clean;

        $this->assertInstanceOf('Illuminate\Console\Command', $command);
    }

    public function testFailed()
    {
        $app = $this->getApplication();

        $app['twig'] = m::mock('Twig_Environment');
        $app['twig']->shouldReceive('getCache');

        $app['files'] = m::mock('Twig_Environment');
        $app['files']->shouldReceive('deleteDirectory');
        $app['files']->shouldReceive('exists')->andReturn(true);

        $command = new Clean;
        $command->setLaravel($app);

        $output = m::mock('Symfony\Component\Console\Output\OutputInterface');
        $output->shouldReceive('writeln')->with('<error>Twig cache failed to be cleaned</error>');

        $command->run(
            new ArrayInput([]),
            $output
        );
    }

    public function testSuccess()
    {
        $app = $this->getApplication();

        $app['twig'] = m::mock('Twig_Environment');
        $app['twig']->shouldReceive('getCache');

        $app['files'] = m::mock('Twig_Environment');
        $app['files']->shouldReceive('deleteDirectory');
        $app['files']->shouldReceive('exists')->andReturn(false);

        $command = new Clean;
        $command->setLaravel($app);

        $output = m::mock('Symfony\Component\Console\Output\OutputInterface');
        $output->shouldReceive('writeln')->with('<info>Twig cache cleaned</info>');

        $command->run(
            new ArrayInput([]),
            $output
        );
    }
}
