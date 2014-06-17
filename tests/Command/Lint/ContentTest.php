<?php

namespace TwigBridge\Tests\Command\Lint;

use Mockery as m;
use Symfony\Component\Console\Output\StreamOutput;
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

        $loader = m::mock('Twig_LoaderInterface');
        $loader->shouldReceive('getSource')->andThrow(new \Twig_Error_Loader('test'));
        $app['twig.loader'] = $loader;

        $command->setLaravel($app);

        $input  = new \Symfony\Component\Console\Input\ArrayInput([
            'filename' => 'foo.txt',
        ]);
        $output = m::mock('Symfony\Component\Console\Output\OutputInterface');
        $output->shouldReceive('isVerbose')->andReturn(StreamOutput::VERBOSITY_QUIET);
        // $output->shouldReceive('writeln')->with('<comment>0/0 valid files</comment>');

        $command->run($input, $output);
    }
}
