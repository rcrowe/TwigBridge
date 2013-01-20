<?php

namespace TwigBridgeTests;

use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Illuminate\Foundation\Application; 
use Illuminate\Container\Container;
use TwigBridge\Console\PrecompileCommand;
use Mockery as m;

class Console_PrecompileCommand extends PHPUnit_Framework_TestCase
{
    public function teardown()
    {
        $mock_dir = __DIR__.'/../fixtures/Console/';
        // Remove the cached templates directory.
        shell_exec('rm -rf '.$mock_dir.'/storage');
    }

    public function testPrecompile()
    {
        // Begin mocking:
        $mock_dir = __DIR__.'/../fixtures/Console/';

        $app = m::mock('Illuminate\Foundation\Application');

        $config = m::mock('Illuminate\Container\Container');
        $config->shouldReceive('get')->andReturnUsing(function($x) {
            $args = func_get_args();
            if ($args[0] == 'twigbridge::extension') {
                return 'twig';
            }

            return array_pop($args);
        });
        
        $finder = new \Illuminate\View\FileViewFinder(
            new \Illuminate\Filesystem\Filesystem(),
            array($mock_dir)
        );

        $view = m::mock('Illuminate\View\Environment');
        $view->shouldReceive('addExtension');
        $view->shouldReceive('getFinder')->andReturn($finder);

        $app->shouldReceive('offsetGet')->with('config')->andReturn($config);
        $app->shouldReceive('offsetGet')->with('path')->andReturn($mock_dir);
        $app->shouldReceive('offsetGet')->with('view')->andReturn($view);
        // End mocking.


        $command = new PrecompileCommand();
        $command->setLaravel($app);
        
        $tester = new CommandTester($command);
        $tester->execute(array());
        $this->assertContains('1 Twig templates precompiled', $tester->getDisplay());
        $this->assertCount(1, glob($mock_dir.'/storage/views/twig/*'));
    }

}