<?php

namespace TwigBridgeTests\Console;

use PHPUnit_Framework_TestCase;
use Mockery as m;
use Illuminate\Foundation\Application;
use Illuminate\View\FileViewFinder;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Tester\CommandTester;
use TwigBridge\Console\CompileCommand;
use Symfony\Component\Finder\Finder;

class CompileCommandTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testViewPaths()
    {
        $paths = array(__DIR__.'/../fixtures/Console/Compile/views');

        $tester = $this->getTester($paths);
        $tester->execute(array());

        $this->assertContains('2 Twig templates compiled', $tester->getDisplay());
    }

    public function testNamespacePath()
    {
        $paths = array();
        $hints = array(
            'egg' => array(__DIR__.'/../fixtures/Console/Compile/packages')
        );

        $tester = $this->getTester($paths, $hints);
        $tester->execute(array());

        $this->assertContains('1 Twig template compiled', $tester->getDisplay());
    }

    public function testAllPaths()
    {
        $paths = array(__DIR__.'/../fixtures/Console/Compile/views');
        $hints = array(
            'egg' => array(__DIR__.'/../fixtures/Console/Compile/packages')
        );

        $tester = $this->getTester($paths, $hints);
        $tester->execute(array());

        $this->assertContains('3 Twig templates compiled', $tester->getDisplay());
    }

    private function getTester(array $paths = array(), array $hints = array())
    {
        $app = $this->getApplication($paths, $hints);

        $command = new CompileCommand();
        $command->setLaravel($app);

        $tester = new CommandTester($command);

        return $tester;
    }

    private function getApplication(array $paths = array(), array $hints = array())
    {
        $app = new Application;
        $app['env'] = 'testing';
        $app->instance('path', __DIR__.'/../fixtures/Console/');

        // Storage path
        $app['path.storage'] = __DIR__;

        // Finder
        $finder = new FileViewFinder(new Filesystem(), $paths);

        if (count($hints) > 0) {
            foreach ($hints as $namespace => $namespace_paths) {
                $finder->addNamespace($namespace, $namespace_paths);
                $paths = array_merge($paths, $namespace_paths);
            }
        }

        // Total number of files across all paths
        $file_finder = new Finder();
        $file_finder->files()->in($paths)->name('*.twig');

        // View
        $view = m::mock('Illuminate\View\Environment');
        $view->shouldReceive('addExtension');
        $view->shouldReceive('getFinder')->andReturn($finder);

        $engine = m::mock('Illuminate\View\View');
        $engine->shouldReceive('render');
        $view->shouldReceive('make')->andReturn($engine);

        $app['view'] = $view;

        // Config
        $config = m::mock('Illuminate\Container\Container');
        $config->shouldReceive('get')->andReturnUsing(function($x) {
            $args = func_get_args();
            if ($args[0] == 'twigbridge::extension') {
                return 'twig';
            }

            return array_pop($args);
        });

        $app['config'] = $config;

        return $app;
    }
}
