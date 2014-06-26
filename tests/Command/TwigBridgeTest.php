<?php

namespace TwigBridge\Tests\Command;

use TwigBridge\Tests\Base;
use Mockery as m;
use TwigBridge\Command\TwigBridge;
use Twig_Environment;
use TwigBridge\Bridge;
use Symfony\Component\Console\Input\ArrayInput;

class TwigBridgeTest extends Base
{
    public function testInstance()
    {
        $command = new TwigBridge;

        $this->assertInstanceOf('Illuminate\Console\Command', $command);
    }

    public function testOutput()
    {
        $command = new TwigBridge;

        $output = m::mock('Symfony\Component\Console\Output\OutputInterface');
        $output->shouldReceive('writeln')->with(
            '<info>Twig</info> version        <comment>'.Twig_Environment::VERSION.'</comment>'
        );
        $output->shouldReceive('writeln')->with(
            '<info>Twig Bridge</info> version <comment>'.Bridge::BRIDGE_VERSION.'</comment>'
        );

        $command->run(
            new ArrayInput([]),
            $output
        );
    }
}
