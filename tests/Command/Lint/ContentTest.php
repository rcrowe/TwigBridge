<?php

namespace TwigBridge\Tests\Command\Lint;

use Mockery as m;
use Symfony\Component\Console\Input\ArrayInput;
use Twig\Error\LoaderError;
use Twig\Loader\LoaderInterface;
use TwigBridge\Command\Lint;

class ContentTest extends Base
{
    public function testInstance()
    {
        $command = new Lint;

        $this->assertInstanceOf('Illuminate\Console\Command', $command);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testEmpty()
    {
        $command = new Lint;
        $app     = $this->getApplication();

        $loader = m::mock(LoaderInterface::class);
        $loader->shouldReceive('getSourceContext')->andThrow(new LoaderError('test'));
        $app['twig.loader'] = $loader;

        $command->setLaravel($app);

        $input  = new ArrayInput([
            'filename' => 'foo.txt',
        ]);
        $output = m::mock('Symfony\Component\Console\Output\NullOutput')->makePartial();

        $command->run($input, $output);
    }
}
